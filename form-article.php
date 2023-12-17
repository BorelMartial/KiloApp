 <?php 
require_once __DIR__.'/database/database.php';

$authDB = require __DIR__.'/database/security.php';
$currentUser =$authDB->isLoggdin();
if(!$currentUser){
  header('Location: /');
}
$articleDB = require_once __DIR__ . '/database/models/ArticleDB.php';

const ERROR_REQUIRED= 'veuillez renseigner ce champs ';
const ERROR_TITLE_TOO_SHORT ='Le titre est trop court ';
const ERROR_CONTENT_TOO_SHORT ='L\'article est trop court ';
const ERROR_IMAGE_URL ='L\'image doit etre une url valide  ';



$erros = [
  'title'=> '',
  'image' =>'',
  'category' =>'',
  'content' =>'',
]; 
$category='';
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id']??'';


if($id){

  $article =$articleDB->fetchOne($id);
  if($article['author']!==$currentUser['id']){
    header('Location: /');
  };
  $title = $article['title'];
  $image = $article['image'];
 
  $category = $article['category'];
  $content = $article['content'];
}
if($_SERVER['REQUEST_METHOD']==='POST'){

 
  $_POST = filter_input_array(INPUT_POST,[
    'title'=> FILTER_SANITIZE_SPECIAL_CHARS,
    'image'=> FILTER_SANITIZE_URL,
    'category'=> FILTER_SANITIZE_SPECIAL_CHARS,
    'content'=> [
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
    ] 
  ]);
  $title = $_POST['title']??'';
  $image = $_POST['image']??'';
 
  $category = $_POST['category']??'';
  $content = $_POST['content']??'';
 
  if(!$title){
    $erros['title']= ERROR_REQUIRED;
  
  }  elseif (mb_strlen($title)<5){
    $erros['title']= ERROR_TITLE_TOO_SHORT;
  }

  if(!$image){
    $erros['image']= ERROR_REQUIRED;
   
  
  }  elseif (!filter_var($image, FILTER_VALIDATE_URL)){
    $erros['image']= ERROR_IMAGE_URL;
  }

  
  if(!$category){
    $erros['category']= ERROR_REQUIRED;
  
  }

  if(!$content){
    echo $content;
    $erros['content']= ERROR_REQUIRED;
  
  }  elseif (mb_strlen($content)<50){
    $erros['content']= ERROR_CONTENT_TOO_SHORT;
  
  }

  if(empty(array_filter($erros, fn($e)=> $e !==''))){
   if($id){
$article['title']=$title;
$article['image']=$image;
$article['category']=$category;
$article['content']=$content;
$article['author']=$currentUser['id'];

$articleDB->updateOne($article);

   } else {
   
   $articleDB->createOne([
    'title'=>$title,
    'content'=> $content,
    'category'=>$category,
    'image'=>$image,
    'author'=> $currentUser['id']
   ]);

   }
   
 header('Location: /');
  }
  


};
?>



 <html lang="en">

 <head>
   <!-- <link rel="stylesheet" href="public/css/form-article.css"> -->
   <?php require_once 'includes/head.php' ?>
   <title> <?= $id? 'Modifier':'Creer'?> un article</title>
 </head>

 <body>

   <div class="container">
     <?php require_once 'includes/header.php'?>

     <div class="content">
       <div class="block p-20 form-container">
         <h1>
           <?= $id? 'Modifier':'Ecrire'?> un article
         </h1>
         <form action="/form-article.php<?=$id? "?id=$id":''?>" , method="POST">
           <div class="form-control">
             <label for="title">Titre</label>
             <input type="text" name="title" id="title" value="<?= $title??'' ?>">
             <?php if($erros['title']): ?>
             <p class="text-danger"><?= $erros['title'] ?></p>
             <?php endif; ?>
           </div>

           <div class="form-control">
             <label for="image">Image</label>
             <input type="text" name="image" id="image" value="<?= $image??'' ?>">
             <?php if($erros['image']): ?>
             <p class="text-danger"><?= $erros['image'] ?></p>
             <?php endif; ?>
           </div>

           <div class="form-control">
             <label for="category">Categorie</label>

             <select name="category" id="category">

               <option <?= !$category || $category==='nature'? 'selected':''?> value="nature">Nature</option>
               <option <?= $category=='technology' ? 'selected':''?> value="technology">Technologie
               </option>
               <option <?= $category=='politic' ? 'selected':''?> value="politic">Politique</option>
             </select>
             <?php if($erros['category']): ?>
             <p class="text-danger"><?= $erros['category'] ?></p>
             <?php endif; ?>
           </div>

           <div class="form-control">
             <label for="content">Contenu</label>
             <textarea type="text" name="content" id="content"> <?= $content??'' ?></textarea>
             <?php if($erros['content']): ?>
             <p class="text-danger"><?= $erros['content'] ?><?= $erros['content'] ?></p>
             <?php endif; ?>
           </div>
           <div class="form-action">
             <a href="/" class="btn btn-secondary" type="button">Annuler</a>
             <button class="btn btn-primary" type="submit"><?=$id? 'Modifier': 'Sauvegarder'?></button>
           </div>
         </form>
       </div>
     </div>

     <?php require_once 'includes/footer.php' ?>
   </div>
 </body>


 </html>