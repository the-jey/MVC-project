<?php
// Paramètres de connexion
$userDB = 'root';
$passwordDB = '';
$nameDB = 'igroupe';
$hoteDB = '127.0.0.1';
// Connexion à la base de données
$bdd = new PDO("mysql:host=$hoteDB; dbname=$nameDB", $userDB, $passwordDB);

// Reqête pour afficher les groupes dans le select
$reqGroupes = $bdd->query('SELECT * from groups');
// On récupère les différents groupes et leur id respective
while($resultat = $reqGroupes->fetch()) {
  $groups[$resultat['groupId']] = $resultat['groupName']; 
}
// De base selected est à false (pour laisser le champ 'choisir un groupe' sélectionné voir plus bas)
$selected = false;
// On regarge s'il y a une requête post avec un groupe
if(!empty($_POST['group'])) {
  $selected = true;
  // On récupère l'id du groupe choisis
  $idFocusGroup = $_POST['group'];
  // On prépare la requête
  $reqArtistes = $bdd->prepare(
    'SELECT a.artistName, a.artistFirstname, a.artistId
    FROM artists a 
    JOIN relationGroupsArtists rga
    ON a.artistId = rga.artistId
    WHERE rga.groupId = ?');
  // On ajoute le paramètre
  $reqArtistes->bindParam(1, $idFocusGroup);
  // On exécute la requête
  $reqArtistes->execute();
  // On récupère les données
  $artists = $reqArtistes->fetchAll();
  // while($resultat = $reqArtistes->fetch()){
  //   $artists[$resultat['artistId']] = [$resultat['artistName'], $resultat['artistFirstname']]; 
  // }
  // // foreach($artists as $id => $artist) {
  // //   echo '<br>';
  // //   foreach($artist as $id => $a) {
  // //     echo $id;
  // //   }
  // // }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>iGroupe</title>
  </head>
  <body class="bg-dark  d-flex align-items-center" style="height: 100vh;">
    <div class="container bg-white pb-5">
      <div class="row">
        <div class="col-12 text-center">
          <h1 class="my-5">iGroup</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-12 text-center">
          <h5>Choisir un groupe dans la liste</h5>
          <form method="POST" action="./">
            <select class="form-control col-4 mx-auto" name="group" id="group" onchange="this.form.submit()">
              <option value="" <?php if($selected === false) {echo 'selected';} ?> >Choisir un groupe</option>
              <?php
              foreach($groups as $id => $group) {
                $id == $idFocusGroup ? $selected = 'selected' : $selected = '';
                echo '<option value="'. $id .'"'. $selected .' >'. $group . '</option>';
              }
              ?>
            </select>
          </form>
        </div>
      </div>
      <div class="row text-center mt-3">
        <div class="col-10 mx-auto">
          <?php
            // Si un groupe est choisi
            if(!empty($_POST['group'])) {
              echo '
                <table class="table">
                  <thead>
                    <tr>
                      <th>Prénom de l\'artiste</th>
                      <th>Nom de l\'artiste</th>
                    </tr>
                  </thead>
                    <tbody>
              ';
                      foreach($artists as $artist) {
                        echo '<tr><td>'.$artist['artistFirstname'].'</td>
                              <td>'.$artist['artistName']. '</td><tr>';                    
                      }
              echo'
                  </tobdy>
                </table>
              ';
            }
          ?>        
        </div>
      </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>