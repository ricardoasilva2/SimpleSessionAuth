SimpleSessionAuth
=================

Uma forma simples de lidar com sessões no PHP, com o **SimpleSessionAuth** você facilmente e rapidamente implementará no seu projeto coisas como:
* Login;
* Logout;
* Validação de sessão;
* Gerenciamento de tempo de sessão;
* Gerenciamento de tempo de ociosidade;
* Validação de regras de usuários;
* Tratamento de erros/mensagens amigável.
 
## Usage / Uso
Para usar, não esqueça de sempre referenciar o aquivo/classe SimpleSessionAuth
```php
use SimpleSessionAuth;
```

### Login
Login de usuário simples.

```php 
if ($user->authenticate($login, $senha)){
        $session = new SimpleSessionAuth();
        // Realiza o login
        $session->logIn();
        // Setanto valores quaisquer na sessão
        $session->set('user_id', $user->getId());
        $session->set('user_name', $user->getName());
}

```
Login de usuário personalizado.
```php 
if ($user->authenticate($login, $senha)){
        $session = new SimpleSessionAuth();
        
        // Realiza o login
        $session->logIn();
        
        // Seta o tempo máximo de sessão
        // Nota: se não definido, como no exemplo anterior, o padrão é 4 horas.
        $session->setExpire(time() + 6 * 60 * 60); // 6 horas
        
        // Seta o tempo máximo de ociosidade
        // Nota: se não definido, como no exemplo anterior, o padrão é 30 minutos.
        $session->setIdle(40 * 60); // 40 minutos
        
        // Setanto valores quaisquer na sessão
        $session->set('user_id', $user->getId());
        $session->set('user_name', $user->getName());
}

```
### Autenticação da sessão
Verificando se usuário está autenticado

```php 
$session = new SimpleSessionAuth();
if (!$session->isAuthenticated()){
       $this->redirect("usuario/login");
       exit;
}            

```

### Logout
Logout de usuário
Atenção, este método limpa todas a variáveis gravadas em sessão

```php 
$session = new SimpleSessionAuth();
$session->logout();         

```

### Mensagens
Pegando mensagens de erros, aviso e sucesso geradas pelo SimpleSessionAuth
Nota 1: Os códigos ds mensagens são gravadas em sessão, sendo assim, você poderá recuperá-la em outras requisições.
Nota 2: Textos amigáveis de mensagens estão gravados em no aquivo SimpleSessionAuthMessages.json, o qual recomendamos que você o personalize como desejar.

#### getArrayMessages()
Pegando os códigos das mensagens aninhadas em Array

```php 
$session = new SimpleSessionAuth();
if ($session->getArrayMessages()){ // se não hover menssagens, retorna false
       $arrayMessages = $session->getArrayMessages();
       $session->clearMessages(); // Limpando as mensagens da sessão
       foreach($arrayMessages as $codeMessage){
              print $codeMessage;
       }
}

```

#### getArrayTextMessages()
Pegando os textos amigáveis das mensagens aninhadas em Array

```php 
$session = new SimpleSessionAuth();
if ($arrayMessages = $session->getArrayTextMessages()){ // se não hover menssagens, retorna false       
       foreach($arrayMessages as $messageTxt){
              print $messageTxt;
       }
       $session->clearMessages(); // Limpando as mensagens da sessão
}

```

#### getTextMessages()
Pegando os textos amigáveis das mensagens aninhadas em String (HTML) de forma padrão.
Ma forma padrão, cada mensagens retorna dentro de uma tag <p /> com o atributo "class" setado como "SimpleSessionAuth_Messages"

```php 
$session = new SimpleSessionAuth();
if ($messagesTxt = $session->getTextMessages()){ // se não hover menssagens, retorna false
       print $messagesTxt;
       $session->clearMessages(); // Limpando as mensagens da sessão
}

```
Pegando os textos amigáveis das mensagens aninhadas em String (HTML) de forma personalizada.
Neste exemplo, cada mensagens retorna dentro de uma tag <div /> com o atributo "class" setado como "warning" e "title" como "Leia com atenção".

```php 
$session = new SimpleSessionAuth();
if ($messagesTxt = $session->getTextMessages('div', Array('class' => 'warning', 'title' => 'Leia com atenção'))){ // se não hover menssagens, retorna false
       print $messagesTxt;
       $session->clearMessages(); // Limpando as mensagens da sessão
}

```
