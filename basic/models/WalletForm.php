<?php
namespace app\models;
use yii\base\Model;
class WalletForm extends Model
{
  public $senderId;
  public $recepientId;
  public $value;
  public function rules()
  {
    return [
        [['senderId', 'recepientId', 'value'], 'required'],
        ['senderId', 'compare', 'compareAttribute' => 'recepientId', 'operator' => '!=', 'message' => 'Please choose a different client'],
      ]; 
  }
}