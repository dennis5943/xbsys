<?php
/***
 * TelnetDog
 * Telnet client in PHP
 *
 * @author William F.
 * @copyright GPL ©2010
 * @version 1.5
 * @package TelnetDog.class
 * @subpackage TelnetDog_FTP.class
*/

class TelnetDog {
 function __construct($h, $p){
  try {
   $this->socket = fsockopen($h, $p);
   $this->host = $h;
   $this->port = $p;
	
	stream_set_timeout($this->socket, 2);

   if(!$this->socket){
    throw new Exception("[TelnetDog] Could not connect to ".$this->host.":".$this->port.".");
   }
  } catch (Exception $ex){
   echo($ex->getMessage());
  } 
 }

 function Close(){
  return fclose($this->socket);
  $this->socket = null;
 }

 function Status(){
  if(!$this->socket){
   return "[TelnetDog] ".$this->host.":".$this->port." - Not connected<br>";
  } else {
   return "[TelnetDog] ".$this->host.":".$this->port." - Connected<br>";
  }
 }

 function Receiving(){
  return "!feof($this->socket)";
 }

 function Execute($c,$containReturn=true){
 	$retLetn = 0;
 	if($containReturn) { $retKey = "\r";$retLen = 1; }
  return fputs($this->socket, ($c.$retKey), strlen($c)+$retLen);
 }

 function Write($c){
  return fwrite($this->socket, $c."\n", strlen($c));
 }

	function Get(){
		$READBYTE = 2048;
		
		$s = stream_get_meta_data($this->socket);
		if(strlen($s['unread_bytes']) == 0 || $s['unread_bytes'] < 0) { $s['unread_bytes'] = 0; }
		//if($s['unread_bytes'] == 0) { return; }
		
		$lengthLeft = min($READBYTE,$s['unread_bytes']);
		if($READBYTE != $lengthLeft) {
			return stream_get_line($this->socket, $lengthLeft)."</br>";
		} else {
			return stream_get_line($this->socket, $lengthLeft,"\r")."</br>";
		}
	}
}
?>