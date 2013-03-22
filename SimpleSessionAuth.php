<?php
namespace libs\SimpleSessionAuth;

/**
* SimpleSessionAuth.
* @author Ricardo Amorim <ricardoasilva2@gmail.com>
*/
class SimpleSessionAuth {
    
    /**
    * Link para arquivo json com as mensagens geradas pelo sistema
    * @see setJsonMessagesUrl()
    */
    private $jsonMessagesUrl = "libs/SimpleSessionAuth/SimpleSessionAuthMessages.json";
    
    /**
    * Construct
    * @return Nothing
    */
    function __construct(){
        $this->start();
    }
    
    /**
    * Start a sessão
    * @see __construct()
    * @return Nothing
    */
    public function start(){
        if (!isset($_SESSION)){
            session_start();
            session_regenerate_id();
        }
    }
    
    /**
    * Pegar uma variável gravada na sessão
    * @param String $name Nome da variável gravada na sessão
    * @param String $default Valor default caso não haja nada na sessão com $name
    * @see isLoggedIn()
    * @return String
    */
    public static function &get($name, $default = null){
        if (!isset($_SESSION[$name]) && isset($default)) {
            $_SESSION[$name] = $default;
        }
        return $_SESSION[$name];
    }

    /**
    * Gravar um valor em uma variável na sessão
    * @param String $name Nome da variável a ser gravada na sessão
    * @param String $value Valor a ser gravado na sessão
    * @see logIn()
    * @return String
    */
    public function set($name, $value){        
        if (null === $value) {
            unset($_SESSION[$name]);
            return null;
        } else {
            $_SESSION[$name] = $value;
            return $_SESSION[$name];
        }
    }
    
    /**
    * Realiza login, seta tempo pra expirar da sessão e seta tempo máximo de ociosidade
    * @param Boolean $setMessage Se false não registra mensagens
    * @return Nothing
    */
    public function logIn($setMessage=true){
        $this->set('__SimpleSessionAuth_Logged', true);
        $this->setExpire(4 * 60 * 60); // expira em 4 horas
        $this->setIdle(30 * 60); // ocioso em 30 min.
        if ($setMessage === true)
            $this->setMessage('SESSION_LOGIN_SUCCESS');
    }
    
    /**
    * Limpa a sessão
    * @param Boolean $setMessage Se false não registra mensagens
    * @return Nothing
    */
    public function logOut($setMessage=true){
        $this->clear();
        if ($setMessage === true)
            $this->setMessage('SESSION_LOGOUT_SUCCESS');
    }
    
    /**
    * Verifica se está logado
    * @param Boolean $setMessage Se false não registra mensagens
    * @see isAuthenticated()
    * @return Boolean
    */
    public function isLoggedIn($setMessage=true){
        if ($this->get('__SimpleSessionAuth_Logged') === true)
            return true;
        
        if ($setMessage === true)
            $this->setMessage('SESSION_NOT_LOGGEDIN');
        
        return false;
    }
    
    /**
    * Verifica se está autenticado
    * @param Array $rules Regras a serem testadas
    * @return Boolean
    */
    public function isAuthenticated($rules=false){
        if (!$this->isLoggedIn() OR $this->isExpired() OR $this->isIdle() OR ($rules !== false AND $this->validateRules($rules))){
            $msgs = $this->getArrayMessages();
            $this->clear();
            $this->setArrayMessages($msgs);
            $this->setUrlRemember();
            return false;
        }else{
            $this->clearUrlRemember();
            $this->updateIdle();
            return true;
        }
    }
    
    /**
    * Destroi a sessão por completo ou apenas uma variável
    * @param String $key variavél a ser destruída
    * @return Nothing
    */
    public function destroy($key=false) {
        if (!$key) {
            $this->clear();
            session_destroy();
        } else {
            unset($_SESSION[$key]);
        }
    }
    
    /**
    * Limpa a sessão
    * @see logOut()
    * @see isAuthenticated()
    * @see destroy()
    * @return Nothing
    */
    public function clear(){	
        session_unset();
    }
    
    /**
    * Seta regras a serem testadas na autenticação
    * @param Array $rules Regras a serem testadas
    * @return Nothing
    */
    public function setRules($rules){
        if (is_array($rules))
            foreach($rules as $key => $rule)                
                $_SESSION['__SimpleSessionAuth_Rules_'.$key] = $rule;        
    }
    
    /**
    * valida regras
    * @param Array $rules Regras a serem testadas
    * @param Array $validateAll Todas as regras passadas devem ser válidas
    * @see isAuthenticated()
    * @return Boolean
    */
    public function validateRules($rules, $validateAll=true){
        if (!is_array($rules)){
            $this->setMessage('SESSION_RULES_NOT_ARRAY');
            return false;
        }
        
        if ($validateAll === true){
            foreach($rules as $key => $rule){
                if (empty($_SESSION['__SimpleSessionAuth_Rules_'.$key]) OR $rule != $_SESSION['__SimpleSessionAuth_Rules_'.$key]){                    
                    $this->setMessage('SESSION_RULES_AT_LEAST_ONE_INVALIDE');
                    return false;
                }
            }
            return true;
        }else{
            foreach($rules as $key => $rule)
                if (!empty($_SESSION['__SimpleSessionAuth_Rules_'.$key]) AND $rule == $_SESSION['__SimpleSessionAuth_Rules_'.$key])                
                    return true;
            $this->setMessage('SESSION_RULES_ALL_INVALIDE');
            return false;
        }
    }
    
    /**
    * Seta tempo de duração da sessão
    * @param Integer $time Tempo em segundos
    * @param Boolean $add Setar true se desejar que $time seja adicionado ao tempo que já existe
    * @see logIn()
    * @return Nothing
    */
    public function setExpire($time, $add=false){
        $_SESSION['__SimpleSessionAuth_Expire'] = !$add ? $time : $_SESSION['__SimpleSessionAuth_Expire'] + $time;        
        if (!isset($_SESSION['__SimpleSessionAuth_Expire_TS'])) $_SESSION['__SimpleSessionAuth_Expire_TS'] = time();
    }
    
    /**
    * Seta tempo de duração máxima de ociosidade da sessão
    * @param Integer $time Tempo em segundos
    * @param Boolean $add Setar true se desejar que $time seja adicionado ao tempo que já existe
    * @see logIn()
    * @return Nothing
    */
    public function setIdle($time, $add=false){
        $_SESSION['__SimpleSessionAuth_Idle'] = !$add ? $time : $_SESSION['__SimpleSessionAuth_Idle'] + $time;        
        if (!isset($_SESSION['__SimpleSessionAuth_Idle_TS'])) $_SESSION['__SimpleSessionAuth_Idle_TS'] = time();
    }
    
    /**
    * Atualiza o tempo de ociosidade
    * @see isAuthenticated()
    * @return Nothing
    */
    public function updateIdle(){
        if (isset($_SESSION['__SimpleSessionAuth_Idle_TS'])) $_SESSION['__SimpleSessionAuth_Idle_TS'] = time();
    }
    
    /**
    * Retorna em segundos, quanto tempo falta para ser considerado ocioso
    * @return Integer
    */
    public function getSessionValidThru(){
        if (!isset($_SESSION['__SimpleSessionAuth_Idle_TS']) || !isset($_SESSION['__SimpleSessionAuth_Idle']))
            return 0;
        else
            return $_SESSION['__SimpleSessionAuth_Idle_TS'] + $_SESSION['__SimpleSessionAuth_Idle'];
    }
    
    /**
    * Testa se sessão está expirada
    * @param Boolean $setMessage Se false não registra mensagens
    * @see isAuthenticated()
    * @return Boolean
    */
    public function isExpired($setMessage=true){
        if ($_SESSION['__SimpleSessionAuth_Expire'] > 0 && isset($_SESSION['__SimpleSessionAuth_Expire_TS']) &&
            ($_SESSION['__SimpleSessionAuth_Expire_TS'] + $_SESSION['__SimpleSessionAuth_Expire']) <= time()){ 
            if ($setMessage === true)
                $this->setMessage('SESSION_EXPIRED');
                
            return true;   
        }else return false;
    }
    
    /**
    * Testa se sessão está ociosa
    * @param Boolean $setMessage Se false não registra mensagens
    * @see isAuthenticated()
    * @return Boolean
    */
    public function isIdle($setMessage=true){
        if ($_SESSION['__SimpleSessionAuth_Idle'] > 0 && isset($_SESSION['__SimpleSessionAuth_Idle_TS']) &&
            ($_SESSION['__SimpleSessionAuth_Idle_TS'] + $_SESSION['__SimpleSessionAuth_Idle']) <= time()){
            if ($setMessage === true)
                $this->setMessage('SESSION_IDLE');
                
            return true;   
        }else return false;
    }
    
    /**
    * Seta a url atual para ser lembrada após um login, por exemplo
    * @param String $url Url a ser lembrada
    * @see isAuthenticated()
    * @return Nothing
    */
    public function setUrlRemember($url=false){
        $_SESSION['__SimpleSessionAuth_UrlRemember'] = !$url ? $_SERVER['SCRIPT_NAME'] : $url;
    }
    
    /**
    * Pega a url a ser lembrada
    * @param String $url Url padrão caso não haja nada a ser lembrado
    * @return String
    */
    public function getUrlRemember($default=false){
        return isset($_SESSION['__SimpleSessionAuth_UrlRemember']) ? $_SESSION['__SimpleSessionAuth_UrlRemember'] : $default;
    }
    
    /**
    * Limpa a url a ser lembrada
    * @see isAuthenticated()
    * @return Nothing
    */
    public function clearUrlRemember(){
        unset($_SESSION['__SimpleSessionAuth_UrlRemember']);
    }
    
    /**
    * Seta uma mensagem a ser mostrada ao usuário final
    * @param String $msg Código da mensagem
    * @see logIn()
    * @see logOut()
    * @see isLoggedIn()
    * @see isExpired()
    * @see isIdle()
    * @return Nothing
    */
    private function setMessage($msg){
        $_SESSION['__SimpleSessionAuth_Messages'][] = $msg;
    }
    
    /**
    * Seta mensagens a serem mostradas ao usuário final
    * @param Array $msgs Código das mensagens
    * @see isAuthenticated()
    * @return Nothing
    */
    private function setArrayMessages($msgs){
        $_SESSION['__SimpleSessionAuth_Messages'] = $msgs;
    }
    
    /**
    * Pega mensagens a serem mostradas ao usuário final
    * @see isAuthenticated()
    * @return Array
    */
    public function getArrayMessages(){
        return isset($_SESSION['__SimpleSessionAuth_Messages']) ? $_SESSION['__SimpleSessionAuth_Messages'] : false ;
    }
    
    /**
    * Pega mensagens amigáveis a serem mostradas ao usuário final
    * @param String $wrapTag Tag html que deseja que cada mensagem esteja inserida. ex: 'div'
    * @param Array $wrapTagAttributes Atributos e valores a serem incluídos na tag $wrapTag
    * @return String
    */
    public function getTextMessages($wrapTag='p', $wrapTagAttributes=Array('class' => 'SimpleSessionAuth_Messages')){
        $msgs = &$_SESSION['__SimpleSessionAuth_Messages'];
        $msgtext = '';
        if (isset($msgs) and is_array($msgs)){            
            foreach($msgs as $msg){
                $msg = $this->getJsonMessageText($msg);
                if (!$wrapTag){
                    $msgtext .= $msg;
                }else{
                    if (is_array($wrapTagAttributes)){
                        $attrs = "";
                        foreach($wrapTagAttributes as $att => $value)
                            $attrs .= ' '.$att.'="'.$value.'"';
                            
                        $msgtext .= "<".$wrapTag.$attrs.">".$msg."</".$wrapTag.">";
                    }else{
                        $msgtext .= "<".$wrapTag.">".$msg."</".$wrapTag.">";
                    }                    
                }
            }
        }
        return $msgtext == '' ? false : $msgtext;
    }
    
    /**
    * Pega mensagens amigáveis a serem mostradas ao usuário final
    * @param String $wrapTag Tag html que deseja que cada mensagem esteja inserida. ex: 'div'
    * @param Array $wrapTagAttributes Atributos e valores a serem incluídos na tag $wrapTag
    * @return String
    */
    public function getArrayTextMessages(){
        $msgs = &$_SESSION['__SimpleSessionAuth_Messages'];
        $msgstext = false;
        if (isset($msgs) and is_array($msgs))
            foreach($msgs as $msg)
                $msgstext[] = $this->getJsonMessageText($msg);
        return $msgstext;
    }
    
    /**
    * Pega mensagen amigável referente ao código no arquivo json, ou retorna código
    * @param String $msgCode Código da mensagem
    * @see getTextMessages()
    * @return String
    */
    private function getJsonMessageText($msgCode){        
        $json = file_exists($this->jsonMessagesUrl) ? file_get_contents($this->jsonMessagesUrl,0,null,null) : null;
        $json_output = json_decode($json);        
        return is_object($json_output) ? $json_output->{$msgCode} : $msgCode;
    }
    
    /**
    * Limpa as mensagens de sessão
    * @return Nothing
    */
    public function clearMessages(){
        unset($_SESSION['__SimpleSessionAuth_Messages']);
    }
    
    /**
    * Seta o path do arquivo Json de Mensagens
    * @param String $url path do arquivo Json de Mensagens
    * @return Nothing
    */
    public function setJsonMessagesUrl($url){
        $this->jsonMessagesUrl = $url;
    }
}