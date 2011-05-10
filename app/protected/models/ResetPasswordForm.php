<?php

/**
 * Class for resetting password
 */
class ResetPasswordForm extends CFormModel
{
	public $email;
	public $passwordReset;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
                        array('email','required','on'=>'getEmailAddress'),
                        array('email','email','on'=>'getEmailAddress'),
                        array('email','emailUrl','on'=>'getEmailAddress'),
			array('passwordReset', 'required','on'=>'resetPassword'),
                        array('passwordReset','boolean','on'=>'resetPassword'),
                        array('passwordReset','emailNewPassword','on'=>'resetPassword'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>'Email address',
                        'passwordReset'=>'Reset my password'
		);
	}

	/**
	 * Sends an email with link to reset password form
	 */
	public function emailUrl()
	{
            $hash = createHash($this->email);
            $headers="From: ".Yii::app()->name."\r\nReset Password";
            $subject="Reset your password";
            $body = "So you forgot your password? Don't worry, it happens to everybody once in a while.\n\n
                This link to be taken to a page where you can reset your password. The new generated password will be emailed to you, so you can login and set a password of your choosing.\n\n
                Click here: ".Yii::app()->createUrl(array('/site/resetPassword','email'=>$this->email,'hash'=>$hash))."\n\n
                - The ".Yii::app()->name." Team.";
            mail($this->email,$subject,$body,$headers);
            Yii::app()->user->setFlash('getEmailAddress','We\'re sending password reset instructions to the email address you gave. This could take up to several minutes.');
            $this->refresh();
       
	}

        /**
	 * Resets password when user clicks 'reset password' button and emails it to them
	 */
	public function emailNewPassword()
	{
            $newPassword = md5($this->email.time().$this->email);

            $headers="From: ".Yii::app()->name."\r\n New Password";
            $subject="Your reset password";
            $body = "Here's your new password. You may log in at the link below and choose a new password in your profile, using this email address and this password.\n\n
                New password: ".$newPassword."\n\n
                Click here: ".Yii::app()->createUrl(array('/site/index'))."\n\n
                - The ".Yii::app()->name." Team.";
            mail($this->email,$subject,$body,$headers);
            Yii::app()->user->setFlash('getEmailAddress','Your new generated password is being sent to your email address. This could take up to several minutes.');
            $this->refresh();

            return true;
	}

        /**
	 * Resets password when user clicks 'reset password' button
	 */
	public function createHash($email)
	{
            $hash='';
            $data = User::model()->find(array(
                'select'=>'first_name,email,state,zip,phone',
                'condition'=>'email=:email',
                'params'=>array(':email'=>$email),
            ));
            if(!empty($data)) {
                $hash = md5($data->first_name(md5($data->email.$data->state.' '.$data->zip).$data->phone));
            }

            return $hash;
	}

}
