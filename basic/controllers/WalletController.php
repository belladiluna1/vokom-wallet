<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use app\models\Clients;
use app\models\Transaction;
use app\models\WalletForm;

class WalletController extends Controller
{
  public function actionIndex()
  {
    $query = Clients::find();
    $clients = $query->orderBy('id')->all();
    foreach ($clients as $item) {
      $clientsList[$item->id] = $item->name;
    }

    $model = new WalletForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate())
    {
      $message = $this->transaction($model);
      return $this->render('wallet-confirm', ['model' => $model, 'text' => $message]);
    } else {
      return $this->render('index', [
        'clientsList' => $clientsList,
        'transaction' => $transaction,
        'model' => $model
      ]);
    } 
  }

  function transaction($model)
  {
    $query = Clients::find()->where(['id'=>$model->senderId])->all();
    $sender = $query[0];
    $query2 = Clients::find()->where(['id'=>$model->recepientId])->all();
    $recepient = $query2[0];
    $value = (int)$model->value;
    $message = '';

    $senderId = $model->senderId;
    $recepientId = $model->recepientId;

    if ($sender->balance >= $value) {
      $newSB = (int)$sender->balance - $value;
      $newRB = (int)$recepient->balance + $value;

      Yii::$app->db->transaction(function($db) use ($senderId, $recepientId, $value, $newSB, $newRB) {

        $sender2 = $db->createCommand('
        SELECT * FROM clients WHERE id = :id FOR UPDATE;
        ')->bindValue(':id', $senderId)->queryOne();
        $recepient2 = $db->createCommand('
        SELECT * FROM clients WHERE id = :id FOR UPDATE;
        ')->bindValue(':id', $recepientId)->queryOne();

        $db->createCommand("UPDATE clients SET balance=:newSB WHERE id=:id")
        ->bindValue(':id', $senderId)
        ->bindValue(':newSB', $newSB)
        ->execute();
        $db->createCommand("UPDATE clients SET balance=:newRB WHERE id=:id")
        ->bindValue(':id', $recepientId)
        ->bindValue(':newRB', $newRB)
        ->execute();

        $db->createCommand()
        ->insert('transaction', ['sender_id' => $senderId, 'recepient_id' => $recepientId, 'value' => $value])
        ->execute();
      });
      $message = 'Вы перевели средства ('.$value.') от '.$sender->name.' к '.$recepient->name;
    }
    else {
      $message = 'У '.$sender->name.' не хватает средств';
    }
    return $message;

  }
}