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
        
        // Setanto valores quaisquer na sessão
        $session->set('user_id', $user->getId());
        $session->set('user_name', $user->getName());
}

```
