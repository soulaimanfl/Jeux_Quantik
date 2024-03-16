<?php

namespace Quantik24;


require_once 'PieceQuantik.php';

echo "<pre>\n";
echo PieceQuantik::initBlackCone()."<br />\n";
echo PieceQuantik::initWhiteCone()."<br />\n";
echo PieceQuantik::initBlackCube()."<br />\n";
echo PieceQuantik::initWhiteCube()."<br />\n";
echo PieceQuantik::initBlackCylindre()."<br />\n";
echo PieceQuantik::initWhiteCylindre()."<br />\n";
echo PieceQuantik::initBlackSphere()."<br />\n";
echo PieceQuantik::initWhiteSphere()."<br />\n";
echo PieceQuantik::initVOID()."<br />\n";
echo "</pre>\n";

/* ** TRACE d'éxécution de ce programme
<pre>
(Co:B)<br />
(Co:W)<br />
(Cu:B)<br />
(Cu:W)<br />
(Cy:B)<br />
(Cy:W)<br />
(Sp:B)<br />
(Sp:W)<br />
(&nbsp;&nbsp;&nbsp;&nbsp;)<br />
</pre>
** */