<?php

namespace entities;

/** @Entity(repositoryClass="repositories\ChatMessageRepository")
 *  @Table(name="chatmessage") */
class ChatMessage {
	
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	
	/** @Column(type="text") */
	protected $text;
	
	/** @Column(type="datetime") */
	protected $timestamp;
	
	/** @OneToOne(targetEntity="Player") */
	protected $author = null;	// not implemented yet
	
	public function getText() {
		return $this->text;
	}
	
	public function setText($value) {
		$this->text = $value;
	}
	
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	public function setTimestamp($value) {
		$this->timestamp = $value;
	}
	    
	
}

?>