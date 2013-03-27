SimpleSessionAuth
=================

Uma forma simples de lidar com sessões no PHP, com o **SimpleSessionAuth** você facilmente e rapidamente implementará no seu projeto coisas como:
* Login;
* Logout;
* Validação de sessão;
* Gerenciamento de tempo de sessão;
* Gerenciamento de tempo de ociosidade;
* Validação de regras de usuários;
* Lembra a URL ao perder a sessão, para redireciona-lo de volta ao logar-se novamente
* Tratamento de erros/mensagens amigável.
 
## Usage / Uso
Para usar, não esqueça de sempre referenciar o aquivo/classe SimpleSessionAuth
```php
use SimpleSessionAuth;
```

### logIn(), set(), setExpire(), setIdle()
Login de usuário simples.
Nota: Em logIn(), Se passado false no primeiro parametro, não é gerado mensagens erros e avisos

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
        $session->setExpire(6 * 60 * 60); // 6 horas
        
        // Seta o tempo máximo de ociosidade
        // Nota: se não definido, como no exemplo anterior, o padrão é 30 minutos.
        $session->setIdle(40 * 60); // 40 minutos
        
        // Setanto valores quaisquer na sessão
        $session->set('user_id', $user->getId());
        $session->set('user_name', $user->getName());
}
```
### isAuthenticated(), get()
Verificando se usuário está autenticado da forma mais simples

```php 
$session = new SimpleSessionAuth();
if ($session->isAuthenticated()){
       // Pega as variáveis gravadas na sessão
       print $session->get('user_id');
       print $session->get('user_name');
       exit;
}
```
### isAuthenticated(), setRules(), validateRules()
Verificando se usuário está autenticado validando algumas regras

```php 
// Pagina de login
$session = new SimpleSessionAuth();
$session->logIn();

// Seta regras do usuário
$session->setRules(Array('permission' => 'admin', 'comments' => 'edit'));

// ############################################

// Outra página
$session = new SimpleSessionAuth();
$teste_rules1 = Array('permission' => 'admin', 'comments' => 'edit');
$teste_rules2 = Array('permission' => 'admin', 'comments' => 'view');
$teste_rules3 = Array('permission' => 'user');
$teste_rules4 = Array('permission' => 'admin');

// validando regras diretamente no isAuthenticated()
// valida se todas as regras do array passado são válidas
$session->isAuthenticated($teste_rules1); // return true
$session->isAuthenticated($teste_rules2); // return false
$session->isAuthenticated($teste_rules3); // return false
$session->isAuthenticated($teste_rules4); // return true

// validando regras no validateRules()
// valida se todas as regras do array passado são válidas
$session->validateRules($teste_rules1); // return true
$session->validateRules($teste_rules2); // return false
$session->validateRules($teste_rules3); // return false
$session->validateRules($teste_rules4); // return true

// validando regras no validateRules() com segundo parametro false
// valida se pelo menos uma das regras do array passado é válida
$session->validateRules($teste_rules1, false); // return true
$session->validateRules($teste_rules2, false); // return true
$session->validateRules($teste_rules3, false); // return false
$session->validateRules($teste_rules4, false); // return true
```
### logOut()
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

#### getArrayMessages(), clearMessages()
Pegando os códigos das mensagens aninhadas em Array

```php 
$session = new SimpleSessionAuth();
if ($arrayMessages = $session->getArrayMessages()){ // se não hover menssagens, retorna false
       foreach($arrayMessages as $codeMessage){
              print $codeMessage;
       }       
       $session->clearMessages(); // Limpando as mensagens da sessão
}
```

#### getArrayTextMessages(), clearMessages()
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

#### getTextMessages(), clearMessages()
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

#### setJsonMessagesUrl()
Seta o path para o arquivo de mensagens amigáveis.
Nota: se o arquivo não for achado, não ocorrerá erros, mas as mensagens retornadas serão seis códigos relativos.

```php 
$session = new SimpleSessionAuth();

// Setando o path para o arquivo de mensagens personalizadas
$session->setJsonMessagesUrl('commons/SimpleSessionAuthMessages.json');

if ($messagesTxt = $session->getTextMessages()){ // se não hover menssagens, retorna false
       print $messagesTxt;
       $session->clearMessages(); // Limpando as mensagens da sessão
}
```

### Métodos públicos que podem ser úteis
Metodos que podem ser utilizados para usos mais avançados e/ou personalizados

#### isLoggedIn()
Verifica se usuário está logado, independente se o tempo de sessão e ociosidade expirou ou não.
Nota: Se passado false no primeiro parametro, não é gerado mensagens erros e avisos

```php 
$session = new SimpleSessionAuth();
if ($session->isLoggedIn()){
       print $session->get('user_id');
       print $session->get('user_name');
       exit;
}
```

#### isExpired()
Verifica se a sessão expirou, independente se usuário está logado e/ou o tempo ociosidade expirou ou não.
Nota: Se passado false no primeiro parametro, não é gerado mensagens erros e avisos

```php 
$session = new SimpleSessionAuth();
if ($session->isExpired()){
       $session->logOut();
}
```

#### isIdle()
Verifica se a o tempo de ociosidade expirou, independente se usuário está logado e/ou o tempo de sessão expirou ou não.
Nota: Se passado false no primeiro parametro, não é gerado mensagens erros e avisos

```php 
$session = new SimpleSessionAuth();
if ($session->isIdle()){
       $session->logOut();
}
```

#### updateIdle()
Atualiza a hora para renovar o tempo de ociosidade
Nota: Sempre que o método isAuthenticated() é executado, o updateIdle() também é automaticamente

```php 
$session = new SimpleSessionAuth();
$session->updateIdle();
```

#### getSessionValidThru()
Retorna quanto tempo ainda tem para o tempo de ociosidade expitar

```php 
$session = new SimpleSessionAuth();

// data e hora que vai se expirar a sessão de ociosidade
print date('d/m/Y - H:i:s', $session->getSessionValidThru());

// minutos para expirar a sessão
print ($session->getSessionValidThru()-time())/60;
```

#### setUrlRemember()
Seta uma URL para ser lembrada posteriormente
Muito útil para quando o usuário perde a sessão, e ao logar-se novamente, ele pode ser redirecionado para a url lembrada
Nota: Sempre que o método isAuthenticated() é executado, o setUrlRemember() também é automaticamente

```php 
$session = new SimpleSessionAuth();
$session->setUrlRemember(); // seta a url atual chamada
$session->setUrlRemember($url); // seta uma url específica
```

#### getUrlRemember(), clearUrlRemember()
Pega a URL a ser lembrada
Muito útil para quando o usuário perde a sessão, e ao logar-se novamente, ele pode ser redirecionado para a url lembrada

```php 
$session = new SimpleSessionAuth();
$session->logIn();
// Pega URL que foi lembrada (na sessão) pelo método isAuthenticated() ou setUrlRemember()
// Nota: Se não tiver nenhuma url gravada na sessão para lembrar, retornará o parametro passado, no caso, 'index/'
$pathToRedirect = $session->getUrlRemember('index/');
// limpa a variável da sessão
$session->clearUrlRemember();
header("location: {$pathToRedirect}");
```

#### destroy()
Destroi uma variável de sessão ou a sessão inteira.

```php 
$session = new SimpleSessionAuth();
$session->destroy('user_name'); //Destroi somente a variável user_id
$session->destroy(); //Destroi toda a sessão.
```

#### clear()
Limpa a sessão.
Nota: Mesmo que Logout, porém, não seta nenhuma mensagem de sucesso de Logout.

```php 
$session = new SimpleSessionAuth();
$session->clear();
```

## TODO
* Realizar mais testes;
* Revisar e melhorar este arquivo README;
* Idéias novas são bem vindas;
* Críticas também.

## Autor
Ricardo Amorim <ricardoasilva2@gmail.com>
