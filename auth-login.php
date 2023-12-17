<pre>
<?php 
require_once './database/database.php';
$authDB = require_once './database/security.php';


const ERROR_REQUIRED= 'veuillez renseigner ce champs ';

const ERROR_PASSWORD_TOO_SHORT='Le mot de passe doit faire au moins 6 carractères';
const ERROR_PASSWORD_MISSMATCH='Le mot de passe n\'est pas valide';
const ERROR_EMAIL_INVALID =' L\'email n\'est pas valide';
const ERROR_EMAIL_UNKOWN='L\'email n\'est pas enregistrée';



$erros = [

  'email' =>'',
  'password' =>'',

]; 
if($_SERVER['REQUEST_METHOD']=='POST'){

  $input = filter_input_array(INPUT_POST,[
  
    'email' =>FILTER_SANITIZE_EMAIL,
  ]); 
 
  $email=$input['email']??'';
  $password = $_POST['password']??'';




if(!$email){
  $erros['email']=ERROR_REQUIRED;
} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
  $erros['email']=ERROR_EMAIL_INVALID;
}

if(!$password){
  $erros['password']=ERROR_REQUIRED;
} elseif(mb_strlen($password)<6){
  $erros['password']=ERROR_PASSWORD_TOO_SHORT;
}





  if(empty(array_filter($erros, fn($e)=> $e !==''))){

$user = $authDB->getUserFromEmail($email);
  if(!$user){
   
    $erros['email']=ERROR_EMAIL_UNKOWN;
  } else {
    if(!password_verify($password, $user['password'])){
      $erros['password']=ERROR_PASSWORD_MISSMATCH;
    } else {
     $authDB->login($user['id']);
      header('Location: /');
    }
  }
  }
  
}

;
?>
</pre>
<html lang="en">

<head>
  <link rel="stylesheet" href="public/css/auth-register.css">
  <?php require_once 'includes/head.php' ?>
  <title>Connexion</title>
</head>

<body>

  <div class="container">
    <?php require_once 'includes/header.php'?>

    <div class="content">

      <div class="block p-20 form-container">
        <h1>
          Connexion
        </h1>
        <form action="/auth-login.php" , method="POST">


          <div class="form-control">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= $email??'' ?>">
            <?php if($erros['email']): ?>
            <p class="text-danger"><?= $erros['email'] ?></p>
            <?php endif; ?>
          </div>

          <div class="form-control">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password">
            <?php if($erros['password']): ?>
            <p class="text-danger"><?= $erros['password'] ?></p>
            <?php endif; ?>
          </div>




          <div class="form-action">
            <a href="/" class="btn btn-secondary" type="button">Annuler</a>
            <button class="btn btn-primary" type="submit">Connexion</button>
          </div>
        </form>
      </div>
    </div>

    <?php require_once 'includes/footer.php' ?>
  </div>
</body>


</html>