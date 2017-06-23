<?php

	class Email {
		
		/**
		 * Link to Smarty module.
		 * @var Smarty
		 */
		private $smarty = null;
		
		/**
		 * Formatted email.
		 * @var HTML
		 */
		private $emailBody = null;
		/**
		 * Email template data.
		 * @var array
		 */
		private $emailData = null;
		
		/**
		 * Instantiate Class.
		 */
		public function __construct(&$smarty) {
			
			$this->smarty =& $smarty;
		
		}
		
		/**
		 * Send email to $email based on template $tpl.
		 * @param array Email template data. Example structure:<br>
		 *	'email' => array (<br>
		 *		&#09;'template' => 'password_reset_success', <b>[Required]</b><br>
		 *		&#09;'subject' => 'You have successfully reset your ' . __oCompanyBrand__ . ' Account password!', <b>[Required]</b><br>
		 *		&#09;'email' => $_post['email'], <b>[Required]</b><br>
		 *		&#09;'description' => 'Password reset confirmation of your ' . __oCompanyBrand__ . ' Account.', <b>[Required]</b><br>
		 *		&#09;'password' => $password, <b>[Other data]</b><br>
		 *		&#09;'loginUrl' => __oCompanyBrandURL__ . '/login' <b>[Other data]</b><br>
		 *	)
		 * @return boolean TRUE on success, FALSE otherwise.
		 */
		public function sendEmail($tplData) {
			
			$this->emailData = $tplData;
			
			return $this->formatEmail()->send();
			
		}
		
		private function formatEmail() {
			
			require __oMOD__ . '/template_settings.php';
			
			//smarty template dir
			$this->smarty->setTemplateDir(__oTPL__ . '/emails');
			
			$this->smarty->assign('Data', array_merge($this->emailData, $data));
			$this->emailBody = 
				$this->smarty->fetch('html_head.tpl') . 
				$this->smarty->fetch($this->emailData['email']['template'] . '.tpl') .
				$this->smarty->fetch('html_foot.tpl')
			;
			
			return $this;
			
		}
		
		private function send() {
		
			$return = true;
			
			$from_name = __oCompanyBrand__ . (__oCI__===true?' CI':'');
			$from_mail = 'noreply@' . __oCompanyDomain__;

			try {
				
				$mailer = new PHPMailer();
				
				// settings
				$mailer->Mailer = 'sendmail';
				$mailer->SingleTo = true;
				
				// sender
				$mailer->setFrom($from_mail, $from_name);
				$mailer->addReplyTo($from_mail, $from_name);
				
				// recipient
				$mailer->addAddress($this->emailData['email']['email'], $this->emailData['email']['email']);
				$mailer->Subject = $this->emailData['email']['subject'];
				$mailer->msgHTML($this->emailBody); //automatically generates AltBody
				
				// send
				$mailer->send();
				
			} catch (Exception $e) {
				trigger_error($e->getMessage()); $return = false;
			}

			return $return;
			
		}

	}
	