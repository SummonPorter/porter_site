<?php

// src/AppBundle/Entity/MandrillEmailOp.php
namespace AppBundle\Entity;

class MandrillEmailOp {

	private $fromEmail;
	private $fromName;
	private $recipients;
	private $subject;
	private $htmlBody;
	private $textBody;

	public function __construct(string $fromEmail_, string $fromName_, string $subject_, string $htmlBody_, string $textBody_) {
		$this->recipients 	= [];
		
		$this->fromEmail 	= $fromEmail_;
		$this->fromName 	= $fromName_;
		$this->subject		= $subject_;
		$this->htmlBody		= $htmlBody_;
		$this->textBody		= $textBody_;
	}

	//	formatted for Mandrill
	public function addRecipient(string $address_, string $type_ = 'bcc', string $name_ = null) {
		$this->recipients[]	= ['email' => $address_, 'type' => $type_, 'name' => $name];
	}

	public function getRecipients(): array {
		return $this->recipients;
	}

	public function getFromEmail(): string {
		return $this->fromEmail;
	}

	public function getFromName(): string {
		return $this->fromName;
	}

	public function getSubject(): string {
		return $this->subject;
	}

	public function getHtmlBody(): string {
		return $this->htmlBody;
	}

	public function getTextBody(): string {
		return $this->textBody;
	}

}