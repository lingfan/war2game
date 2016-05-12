<?php

/**
 *
 * Email模块
 * @author william.hu
 * @version 2010/10/12
 */
class M_Email {
	/**
	 * email发送
	 * @param string $subject 邮件主题
	 * @param string $content 邮件内容
	 * @param string $email 接受者
	 * @param string $username 接受者名称
	 */
	static public function send($subject, $content, $email, $username = '') {
		Loader::loadLib('PHPMailer/class.phpmailer.php');
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SetLanguage('zh_cn');
		try {
			$mail->SMTPDebug  = 2; // enables SMTP debug information (for testing)
			$mail->SMTPAuth   = true; // enable SMTP authentication
			$mail->SMTPSecure = "ssl"; // sets the prefix to the servier
			$mail->Host       = EMAIL_HOST; // sets GMAIL as the SMTP server
			$mail->Port       = EMAIL_PORT; // set the SMTP port for the GMAIL server
			$mail->Username   = EMAIL_USERNAME; // GMAIL username
			$mail->Password   = EMAIL_PASSWORD; // GMAIL password
			$mail->AddAddress($email, $username);
			$mail->SetFrom(EMAIL_USERNAME, 'VPMVC');
			$mail->Subject = $subject;
			$mail->MsgHTML($content);
			$ret = $mail->Send();

			$msg = array(
				'function' => __METHOD__,
				'error'    => '发送成功!',
				'params'   => $email,
			);
			Logger::debug(serialize($msg));
			return $ret;

		} catch (phpmailerException $e) {
			$msg = array(
				'function' => __METHOD__,
				'error'    => $e->errorMessage(),
				'params'   => array(EMAIL_HOST, $email),
			);
			Logger::error(serialize($msg));
		} catch (Exception $e) {
			$msg = array(
				'function' => __METHOD__,
				'error'    => $e->getMessage(),
				'params'   => array(EMAIL_HOST, $email),
			);
			Logger::error(serialize($msg));
		}
	}
}