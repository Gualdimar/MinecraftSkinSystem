<?php

// -----------------------------------------------
// Cryptographp v1.4
// (c) 2006-2007 Sylvain BRISON 
//
// www.cryptographp.com 
// cryptographp@alphpa.com 
//
// Licence CeCILL modifi裍
// => Voir fichier Licence_CeCILL_V2-fr.txt)
// -----------------------------------------------


// -------------------------------------
// Configuration du fond du cryptogramme
// -------------------------------------

$cryptwidth  = 130;  // Largeur du cryptogramme (en pixels)
$cryptheight = 40;   // Hauteur du cryptogramme (en pixels)

$bgR  = 255;         // Couleur du fond au format RGB: Red (0->255)
$bgG  = 255;         // Couleur du fond au format RGB: Green (0->255)
$bgB  = 255;         // Couleur du fond au format RGB: Blue (0->255)

$bgclear = false;     // Fond transparent (true/false)
                     // Uniquement valable pour le format PNG

$bgimg = '';                 // Le fond du cryptogramme peut-鳲e une image  
                             // PNG, GIF ou JPG. Indiquer le fichier image
                             // Exemple: $fondimage = 'photo.gif';
				                     // L'image sera redimensionn裠si n袥ssaire
                             // pour tenir dans le cryptogramme.
                             // Si vous indiquez un r该rtoire plut󲟱u'un 
                             // fichier l'image sera prise au hasard parmi 
                             // celles disponibles dans le r该rtoire

$bgframe = true;    // Ajoute un cadre de l'image (true/false)


// ----------------------------
// Configuration des caract籥s
// ----------------------------

// Couleur de base des caract籥s

$charR = 0;     // Couleur des caract籥s au format RGB: Red (0->255)
$charG = 0;     // Couleur des caract籥s au format RGB: Green (0->255)
$charB = 0;     // Couleur des caract籥s au format RGB: Blue (0->255)

$charcolorrnd = true;      // Choix al蠴oire de la couleur.
$charcolorrndlevel = 2;    // Niveau de clart矤es caract籥s si choix al蠴oire (0->4)
                           // 0: Aucune s諥ction
                           // 1: Couleurs tr籠sombres (surtout pour les fonds clairs)
                           // 2: Couleurs sombres
                           // 3: Couleurs claires
                           // 4: Couleurs tr籠claires (surtout pour fonds sombres)

$charclear = 10;   // Intensit矤e la transparence des caract籥s (0->127)
                  // 0=opaques; 127=invisibles
	                // interessant si vous utilisez une image $bgimg
	                // Uniquement si PHP >=3.2.1

// Polices de caract籥s

//$tfont[] = 'Alanden_.ttf';       // Les polices seront al蠴oirement utilis褳.
//$tfont[] = 'bsurp___.ttf';       // Vous devez copier les fichiers correspondants
//$tfont[] = 'ELECHA__.TTF';       // sur le serveur.
$tfont[] = 'luggerbu.ttf';         // Ajoutez autant de lignes que vous voulez   
//$tfont[] = 'RASCAL__.TTF';       // Respectez la casse ! 
//$tfont[] = 'SCRAWL.TTF';  
//$tfont[] = 'WAVY.TTF';   


// Caracteres autoris豍
// Attention, certaines polices ne distinguent pas (ou difficilement) les majuscules 
// et les minuscules. Certains caract籥s sont faciles ힹ���onfondre, il est donc
// conseill矤e bien choisir les caract籥s utilis豮

$charel = 'ABCDEFGHKLMNPRTWXYZ234569';       // Caract籥s autoris豍

$crypteasy = true;       // Cr蠴ion de cryptogrammes "faciles ힹ���ire" (true/false)
                         // compos豠alternativement de consonnes et de voyelles.

$charelc = 'BCDFGHKLMNPRTVWXZ';   // Consonnes utilis褳 si $crypteasy = true
$charelv = 'AEIOUY';              // Voyelles utilis褳 si $crypteasy = true

$difuplow = false;          // Diff豥ncie les Maj/Min lors de la saisie du code (true, false)

$charnbmin = 5;         // Nb minimum de caracteres dans le cryptogramme
$charnbmax = 6;         // Nb maximum de caracteres dans le cryptogramme

$charspace = 20;        // Espace entre les caracteres (en pixels)
$charsizemin = 14;      // Taille minimum des caract籥s
$charsizemax = 16;      // Taille maximum des caract籥s

$charanglemax  = 25;     // Angle maximum de rotation des caracteres (0-360)
$charup   = true;        // D诬acement vertical al蠴oire des caract籥s (true/false)

// Effets suppl謥ntaires

$cryptgaussianblur = false; // Transforme l'image finale en brouillant: m賨ode Gauss (true/false)
                            // uniquement si PHP >= 5.0.0
$cryptgrayscal = false;     // Transforme l'image finale en d覲ad矤e gris (true/false)
                            // uniquement si PHP >= 5.0.0

// ----------------------
// Configuration du bruit
// ----------------------

$noisepxmin = 10;      // Bruit: Nb minimum de pixels al蠴oires
$noisepxmax = 10;      // Bruit: Nb maximum de pixels al蠴oires

$noiselinemin = 1;     // Bruit: Nb minimum de lignes al蠴oires
$noiselinemax = 1;     // Bruit: Nb maximum de lignes al蠴oires

$nbcirclemin = 1;      // Bruit: Nb minimum de cercles al蠴oires 
$nbcirclemax = 1;      // Bruit: Nb maximim de cercles al蠴oires

$noisecolorchar  = 3;  // Bruit: Couleur d'ecriture des pixels, lignes, cercles: 
                       // 1: Couleur d'袲iture des caract籥s
                       // 2: Couleur du fond
                       // 3: Couleur al蠴oire
                       
$brushsize = 1;        // Taille d'ecriture du princeaiu (en pixels) 
                       // de 1 ힹ���5 (les valeurs plus importantes peuvent provoquer un 
                       // Internal Server Error sur certaines versions de PHP/GD)
                       // Ne fonctionne pas sur les anciennes configurations PHP/GD

$noiseup = false;      // Le bruit est-il par dessus l'ecriture (true) ou en dessous (false) 

// --------------------------------
// Configuration syst笥 & s袵rit狊// --------------------------------

$cryptformat = "png";   // Format du fichier image g诩r瞢GIF", "PNG" ou "JPG"
				                // Si vous souhaitez un fond transparent, utilisez "PNG" (et non "GIF")
				                // Attention certaines versions de la bibliotheque GD ne gerent pas GIF !!!

$cryptsecure = "md5";    // M賨ode de crytpage utilis裺 "md5", "sha1" ou "" (aucune)
                         // "sha1" seulement si PHP>=4.2.0
                         // Si aucune m賨ode n'est indiqu裬 le code du cyptogramme est stock瞍
                         // en clair dans la session.
                       
$cryptusetimer = 0;        // Temps (en seconde) avant d'avoir le droit de reg诩rer un cryptogramme

$cryptusertimererror = 3;  // Action ힹ���蠬iser si le temps minimum n'est pas respect縍
                           // 1: Ne rien faire, ne pas renvoyer d'image.
                           // 2: L'image renvoy裠est "images/erreur2.png" (vous pouvez la modifier)
                           // 3: Le script se met en pause le temps correspondant (attention au timeout
                           //    par d襡ut qui coupe les scripts PHP au bout de 30 secondes)
                           //    voir la variable "max_execution_time" de votre configuration PHP

$cryptusemax = 1000;  // Nb maximum de fois que l'utilisateur peut g诩rer le cryptogramme
                      // Si d诡ssement, l'image renvoy裠est "images/erreur1.png"
                      // PS: Par d襡ut, la dur裠d'une session PHP est de 180 mn, sauf si 
                      // l'hebergeur ou le d赥loppeur du site en ont d袩d矡utrement... 
                      // Cette limite est effective pour toute la dur裠de la session. 
                      
$cryptoneuse = false;  // Si vous souhaitez que la page de verification ne valide qu'une seule 
                       // fois la saisie en cas de rechargement de la page indiquer "true".
                       // Sinon, le rechargement de la page confirmera toujours la saisie.                          
                      
?>
