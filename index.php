<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>Andromeda to Solidpepper extraction tool </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Andromeda">
    <meta name="author" content="Mathieu Dubroca">

    <!-- Le style -->
    <style>
          body {
        text-align: center;
          }
          form {
              display: inline-block;
          }
    </style>

  </head>

  <body>
    <div class="container-fluid">

    <img
        src="img/ripcurl.png" 
        height="60px" 
        width="100px" 
    />

</br>
<br>
<h1 align="center">Andromeda to Solidpepper extraction tool</h1><br>
<table align="center" >
    <tr>
        <td><img src="img/andromeda.png" height=70 width=70></img></th>
        <td><img src="img/excel.png"  height=45 width=45></img></th>
        <td><img src="img/fleche.png"  height=50 width=60></img></th>
        <td><img src="img/engrenage.png"  height=90 width=110></img></th>
        <td><img src="img/fleche.png"  height=50 width=60></img></th>
        <td><img src="img/zip.png"  height=40 width=40></img></th> 
        <td><img src="img/solidpepper.png"  height=60 width=60></img></th>         
    </tr>
</table>

<form action="pictures.php" method="post" enctype="multipart/form-data">

	<script>
	 function onlyOne(checkbox) {
		var checkboxes = document.getElementsByName('csvformat')
		checkboxes.forEach((item) => {
					if (item !== checkbox) item.checked = false
				})
			}
  </script>
       
      <br>
      <br>
			<label for="csvformat"><b>Choose your CSV file format :</b>&nbsp;&nbsp;<br><br>
			<label for="csvcomma">RCA/RCU (CSV comma delimited)&nbsp;&nbsp;<input type="checkbox" name="csvformat" onclick="onlyOne(this)" value=","></label><br>
			<label for="csvcolon">RCE (CSV semi colon delimited)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="csvformat" onclick="onlyOne(this)" value=";"></label><br>

<br>
<br>
			
	<label for="avatar"><b>Upload Andromeda xlsx file :</b></label>
	<br><br>
	
    <input  class="bouton-envoi" type="file" name="mon_fichier">
    <input class="bouton-import" type="submit" name="send" value="Send">
	</form>
  <br>
</body>
</html>