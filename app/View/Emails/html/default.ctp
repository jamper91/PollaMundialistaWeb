<?php
//$content = explode("\n", $content);
//
//foreach ($content as $line):
//	echo '<p> ' . $line . "</p>\n";
//endforeach;
?>

<table class="w580" cellpadding="0" cellspacing="0" border="0" width="580">
    <tbody><tr>
            <td class="w580" width="580">
                <p class="article-title" align="left"><singleline label="Title">Hola <?php echo $nombreUsuario; ?></singleline></p>
<table cellpadding="0" cellspacing="0" align="right" border="0">
    <tbody><tr>
            <td class="w30" width="15"></td>
            <td>
                <?php echo $this->Html->image("image_content_high_46468244_20140320102404.111126.jpg", array('fullBase' => true,"class"=>"w300","border"=>0,"width"=>300));?>
<!--                <img editable="true" label="Image" class="w300" border="0" width="300" src="images/image_content_high_46468244_20140320102404.111126.jpg">-->
            </td>
        </tr>
        <tr><td class="w30" height="5" width="15"></td><td></td></tr>
    </tbody></table>
<div class="article-content" align="left">
    <multiline label="Description">
        Te envio este correo para informarte que acabo de crear una polla y deseo que te unas, dando clic en el siguiente link: <a href="#"> unirse </a><br>

        Con los siguiente datos: <br>
        Nombre Polla:  	<?php echo $nombreBet; ?> <br>
        Id Polla:	<?php echo $idBet; ?>   <br>
    </multiline>
</div>
</td>
</tr>
<tr><td class="w580" height="10" width="580"></td></tr>
</tbody></table>