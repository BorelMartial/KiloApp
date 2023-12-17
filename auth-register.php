<pre>
<?php 
$pdo = require_once './database/database.php';
$authDB =require_once './database/security.php';

const ERROR_REQUIRED= 'veuillez renseigner ce champs ';
const ERROR_TOO_SHORT ='ce champs est trop petit';
const ERROR_PASSWORD_TOO_SHORT='Le mot de passe doit faire au moins 6 carractères';
const ERROR_PASSWORD_MISSMATCH='Le mot de passe de confirmation est différent';
const ERROR_EMAIL_INVALID =' L\'email n\'est pas valide';



$erros = [
  'firstname'=> '',
  'lastname' =>'',
  'email' =>'',
  'password' =>'',
  'confirmpassword' =>''
]; 
if($_SERVER['REQUEST_METHOD']=='POST'){

  $input = filter_input_array(INPUT_POST,[
    'firstname' =>FILTER_SANITIZE_SPECIAL_CHARS,
    'lastname' =>FILTER_SANITIZE_SPECIAL_CHARS,
    'email' =>FILTER_SANITIZE_EMAIL,
  ]); 
  $firstname = $input['firstname']??'';
  $lastname =$input['lastname']??'';
  $email=$input['email']??'';
  $password = $_POST['password']??'';
  $confirmpassword =$_POST['confirmpassword']??'';

if(!$firstname){
  $erros['firstname']=ERROR_REQUIRED;
} elseif(mb_strlen($firstname)<2){
  $erros['firstname']=ERROR_TOO_SHORT;
}

if(!$lastname){
  $erros['lastname']=ERROR_REQUIRED;
} elseif(mb_strlen($lastname)<2){
  $erros['lastname']=ERROR_TOO_SHORT;
}

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

if(!$confirmpassword){
  $erros['confirmpassword']=ERROR_REQUIRED;
} elseif($confirmpassword!==$password){
  $erros['confirmpassword']=ERROR_PASSWORD_MISSMATCH;
}



  if(empty(array_filter($erros, fn($e)=> $e !==''))){
$authDB->register([
  'firstname'=>$firstname,
  'lastname'=> $lastname,
  'email'=> $email,
  'password'=>$password
]);
 header('Location: /');
  }
  
}

;
?>
</pre>
<html lang="en">

<head>
  <link rel="stylesheet" href="public/css/auth-register.css">
  <?php require_once 'includes/head.php' ?>
  <title>Inscription</title>
</head>

<body>

  <div class="container">
    <?php require_once 'includes/header.php'?>

    <div class="content">

      <div class="block p-20 form-container">
        <h1>
          Inscription
        </h1>
        <form action="/auth-register.php" , method="POST">
          <div class="form-control">
            <label for="title">Prenom</label>
            <input type="text" name="firstname" id="firstname" value="<?= $firstname??'' ?>">
            <?php if($erros['firstname']): ?>
            <p class="text-danger"><?= $erros['firstname'] ?></p>
            <?php endif; ?>
          </div>

          <div class="form-control">
            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" value="<?= $lastname??'' ?>">
            <?php if($erros['lastname']): ?>
            <p class="text-danger"><?= $erros['lastname'] ?></p>
            <?php endif; ?>
          </div>

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


          <div class="form-control">
            <label for="confirmpassword">Confirmation du Mot de passe</label>
            <input type="password" name="confirmpassword" id="confirmpassword">
            <?php if($erros['confirmpassword']): ?>
            <p class="text-danger"><?= $erros['confirmpassword'] ?></p>
            <?php endif; ?>
          </div>

          <div class="form-action">
            <a href="/" class="btn btn-secondary" type="button">Annuler</a>
            <button class="btn btn-primary" type="submit">Valider</button>
          </div>
        </form>
      </div>
    </div>

    <?php require_once 'includes/footer.php' ?>
  </div>
</body>


</html>