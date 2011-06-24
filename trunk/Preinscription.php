<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Préinscription</title>
</head>

<body>
<form action="" method="post" name="Inscription" style="float:left">
<fieldset>
<legend>Préinscription</legend>
	<label for="Nom">Nom :</label>
	<input name="Nom" type="text" id="nom"/>
	<label for="Prenom">Prénom :</label>
	<input name="Prenom" type="text" id="prenom"/>
    <br />
    <label for="jour_naissance">Date de naissance :</label>
    <input name="jour_naissance" type="text" value="JJ" size="2" maxlength="2" />
    <input name="mois_naissance" type="text" value="MM" size="2" maxlength="2" />
    <input name="anne_naissance" type="text" value="AAAA" size="4" maxlength="4" />
    <br />
    <label for="mail">Mail :</label>
    <input name="mail" type="text" value="@" id="mail"/>
    <hr />
    <input type="radio" name="choix" id="radio1" value="1"/>
	<label for="radio1">Licencié FFV</label>

	<input type="radio" name="choix" id="radio2" value="2"/>
	<label for="radio2">Pas encore licencié</label>

	<input type="radio" name="choix" id="radio3" value="3"/>
	<label for="radio3">Coureur étranger</label>
    <span id="esp3"><br /></span>
    <label id="l_lic" for="lic">Numéro Licence :</label>
    <input name="lic" id="lic" type="text" maxlenght="8"/>
    <span id="esp1"><hr /></span>
    <input type="radio" name="choix2" id="radio4" value="4"/>
	<label for="radio4" id="l_ls">Laser standard</label>

	<input type="radio" name="choix2" id="radio5" value="5"/>
	<label for="radio5" id="l_lr">Laser radial</label>

	<input type="radio" name="choix2" id="radio6" value="6"/>
	<label for="radio6" id="l_l47">Laser 4.7</label>
    <span id="esp4"><br /></span>
    <label for="numv" id="l_numv">Numéro de voile :</label>
    <input name="numv" type="text" value="FRA" id="numv"/>
    <span id="esp2"><hr /></span>
    <input type="submit" name="maSoumission" id="soumission" value="Valider"/>
</fieldset>

</form>
<script type="text/javascript">
  var cas_FFV = document.getElementById('radio1');
  var cas_nonlic = document.getElementById('radio2');
  var cas_etr = document.getElementById('radio3');
  var valider = document.getElementById('soumission');

  cas_FFV.onclick = function()
  {
  	  document.getElementById('esp1').innerHTML = '<span id="esp1"><hr /></span>';
  	  document.getElementById('esp2').innerHTML = '<span id="esp2"><hr /></span>';
  	  document.getElementById('esp3').innerHTML = '<span id="esp3"><br /></span>';
  	  document.getElementById('esp4').innerHTML = '<span id="esp4"><br /></span>';
	  document.getElementById('radio4').type='radio';
   	  document.getElementById('radio5').type='radio';
   	  document.getElementById('radio6').type='radio';
	  document.getElementById('lic').type='text';
	  document.getElementById('l_lic').innerHTML = '<label id="l_lic" for="lic">Numeros Licence :</label>';
  	  document.getElementById('soumission').type='submit';
   	  document.getElementById('numv').value='FRA';
	  document.getElementById('numv').type='text';
	  document.getElementById('l_numv').innerHTML = '<label for="numv" id="l_numv">Numeros de voile :</label>';
	  document.getElementById('l_ls').innerHTML = '<label for="radio4" id="l_ls">Laser strandard</label>';
	  document.getElementById('l_lr').innerHTML = '<label for="radio5" id="l_lr">Laser radial</label>';
	  document.getElementById('l_l47').innerHTML = '<label for="radio6" id="l_l47">Laser 4.7</label>';
  }
  cas_nonlic.onclick = function()
  {
	  document.getElementById('esp1').innerHTML = '<span id="esp1"></span>';
	  document.getElementById('esp2').innerHTML = '<span id="esp2"></span>';
	  document.getElementById('esp3').innerHTML = '<span id="esp3"></span>';
  	  document.getElementById('esp4').innerHTML = '<span id="esp4"></span>';
  	  document.getElementById('radio4').type='hidden';
  	  document.getElementById('radio5').type='hidden';
  	  document.getElementById('radio6').type='hidden';
	  document.getElementById('l_ls').innerHTML = '<label for="radio4" id="l_ls"></label>';
	  document.getElementById('l_lr').innerHTML = '<label for="radio5" id="l_lr"></label>';
	  document.getElementById('l_l47').innerHTML = '<label for="radio6" id="l_l47"></label>';
  	  document.getElementById('l_lic').innerHTML = '<p><hr />Inscrivez-vous bla bla bla</p>';
  	  document.getElementById('lic').type='hidden';
	  document.getElementById('soumission').type='hidden';
   	  document.getElementById('numv').type='hidden';
	  document.getElementById('l_numv').innerHTML = '<label for="numv" id="l_numv"></label>';
  }
  cas_etr.onclick = function()
  {
  	  document.getElementById('esp1').innerHTML = '<span id="esp1"><hr /></span>';
  	  document.getElementById('esp2').innerHTML = '<span id="esp2"><hr /></span>';
  	  document.getElementById('esp3').innerHTML = '<span id="esp3"></span>';
  	  document.getElementById('esp4').innerHTML = '<span id="esp4"><br /></span>';
   	  document.getElementById('radio4').type='radio';
   	  document.getElementById('radio5').type='radio';
   	  document.getElementById('radio6').type='radio';
	  document.getElementById('lic').type='hidden';
	  document.getElementById('l_lic').innerHTML = '<p><hr />Coureur etrangers bla bla bla</p>';
  	  document.getElementById('soumission').type='submit';
   	  document.getElementById('numv').value='';
	  document.getElementById('numv').type='text';
	  document.getElementById('l_numv').innerHTML = '<label for="numv" id="l_numv">Numeros de voile :</label>';
	  document.getElementById('l_ls').innerHTML = '<label for="radio4" id="l_ls">Laser strandard</label>';
	  document.getElementById('l_lr').innerHTML = '<label for="radio5" id="l_lr">Laser radial</label>';
	  document.getElementById('l_l47').innerHTML = '<label for="radio6" id="l_l47">Laser 4.7</label>';
	  
}
  valider.onclick = function()
  {
	  var nom = document.getElementById('nom').value;
	  var prenom = document.getElementById('prenom').value;
	  var mail = document.getElementById('mail').value;
	  alert(prenom+" "+nom+" vous allez recevoir un couriel sur \n"+mail+"\nCe message vous permetra de confirmer votre préinscription.\nVous avez 30min pour valider votre prèinscription.")
  }	 
</script>
</body>
</html>
