<?php
/*
 *
 * Email: ergomicomseosem@gmail.com
 */
class Response
{
  private $status = 200;
  private $headers = array();
  private $body = null;
  private $request;

  public function __construct(Request $request=null, $body = null, $status = 200, array $headers = array()){
    
    $this->request = $request;
		foreach ($headers as $key => $value)
		{
			$this->set_header($key, $value);
		}
		$this->body = $body;
		$this->status = $status;
	}

  public function reset(){
    $this->status = 200;
    $this->headers = array();
    $this->body = null;
  }

	public function redirect($url = '', $method = 'location', $code = 302){
      $response = new static;
		$response->set_status($code);
		if ($method == 'location'){
			$response->set_header('Location', $url);
		}
		elseif ($method == 'refresh'){
			$response->set_header('Refresh', '0;url='.$url);
		}
		else{
			return;
		}
      $response->send(true);
      exit;
	}

	public function redirect_back($url = '', $method = 'location', $code = 302){
      if ($_SERVER['REDIRECT_URL'] == $url)
      {
        throw new Exception('You can not redirect back here!');
      }
		$this->redirect($url, $method, $code);
	}

	public function set_status($status = 200)
	{
		$this->status = $status;
		return true;
	}

	public function set_header($name, $value, $replace = true){
		if($replace){
			$this->headers[$name] = $value;
		}else{
			$this->headers[] = array($name, $value);
		}
		return true;
	}

	public function get_header($name = null){
		if(func_num_args())
		{
			return isset($this->headers[$name]) ? $this->headers[$name] : null;
		}
		else
		{
			return $this->headers;
		}
	}

  public function appendBody($value){
    if ($value != ""){
      $this->body += $value;
      return true;
    }
  }

	public function body($value = false){
    if (func_num_args()){
      $this->body = $value;
      return true;
    }
    return $this->body;
	}

	public function send_headers(){
		if (!headers_sent()){
			if(isset($_SERVER['FCGI_SERVER_VERSION'])) {
        header('Status: '.$this->status.' '.$this->statuses[$this->status]);
      }else{
				$protocol = $_SERVER['SERVER_PROTOCOL'] ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
				header($protocol.' '.$this->status);
			}
			foreach ($this->headers as $name => $value){
				// Parse non-replace headers
				if (is_int($name) and is_array($value)){
					 isset($value[0]) and $name = $value[0];
					 isset($value[1]) and $value = $value[1];
				}
				// Create the header
				is_string($name) and $value = "{$name}: {$value}";
				// Send it
				header($value, true);
			}
			return true;
		}
		return false;
	}

  public function send($send_headers = false){
		$body = (string) $this->body;
		if ($send_headers){
			$this->send_headers();
		}
		if ($this->body != null){
          $this->send_headers();
          echo $this->body;
		}
	}

  public function response($body='', $status = 200, $contentType='text/html'){
    //$contentType = $this->getContentTypeRequest($contentTypeRequest);
    $this->set_header('Content-Type', $contentType.'; charset=utf-8');
    $this->set_status($status);
    $this->body($body);
    
    $this->send();
  }
  
}
