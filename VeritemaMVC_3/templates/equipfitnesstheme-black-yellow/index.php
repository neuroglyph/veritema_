<?php

/**

 * @package		UD-Equip Joomla Sports Fitness Theme

 * @copyright 	Copyright (c) 2012 - Justin M. @ webunderdog - www.webunderdog.com

 * @license 	GNU General Public License version 3 or later

 */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>

<jdoc:include type="head" />

<link rel="stylesheet" href="templates/_system/css/general.css" type="text/css" />

<link rel="stylesheet" href="templates/<?php echo $this->template ?>/css/template.css" type="text/css" />

<link href="css/template.css" rel="stylesheet" type="text/css" />

<?php require_once('snippets/feature.php'); ?>

<?php require_once('snippets/utility.php'); ?>

<?php require_once('snippets/maintop.php'); ?>

<?php require_once('snippets/mainbottom.php'); ?>

<?php require_once('snippets/bottom.php'); ?>

<?php require_once('snippets/footer.php'); ?>

<?php require_once('snippets/spotlight.php'); ?>

<?php

if($this->countModules('left and right') == 0) $contentwidth = "100";

if($this->countModules('left or right') == 1) $contentwidth = "75";

if($this->countModules('left and right') == 1) $contentwidth = "50";

?>

</head>

<body>

<div id="wrapper">

	<div id="headerwrap"><div id="header">

    	<div id="logo"><?php if($this->countModules('logo')) : ?><jdoc:include type="modules" name="logo" style="xhtml" /><?php endif; ?></div>

        <div id="tagline"><?php if($this->countModules('tagline')) : ?><jdoc:include type="modules" name="tagline" style="xhtml" /><?php endif; ?></div>

        <div id="headinfo"><?php if($this->countModules('headinfo')) : ?><jdoc:include type="modules" name="headinfo" style="xhtml" /><?php endif; ?></div>

    </div></div>

	<div id="navdivwrap"><div id="navdiv">

    	<div id="navmenu"><?php if($this->countModules('navmenu')) : ?><jdoc:include type="modules" name="navmenu" style="" /><?php endif; ?></div>

    </div></div>

    <div id="maindivwrap">

  <div id="container">

	<div id="topspacer"><?php if($this->countModules('topspacer')) : ?><jdoc:include type="modules" name="topspacer" style="xhtml" /><?php endif; ?></div>

        <div id="boxcontent">

        

<?php if($this->countModules('utility1')) : ?><div id="utility">

	<div id="utility1"><jdoc:include type="modules" name="utility1" style="xhtml" /></div><?php endif; ?>

    <?php if($this->countModules('utility2')) : ?><div id="utility2"><jdoc:include type="modules" name="utility2" style="xhtml" /></div><?php endif; ?>

    <?php if($this->countModules('utility3')) : ?><div id="utility3"><jdoc:include type="modules" name="utility3" style="xhtml" /></div></div><?php endif; ?>



        

       <?php if($this->countModules('showcasewide')) : ?><div id="showcasewide"><jdoc:include type="modules" name="showcasewide" style="xhtml" /></div><?php endif; ?> 

       

		<?php if($this->countModules('showcase1')) : ?><div id="showcase">

        	<div id="showcase1"><jdoc:include type="modules" name="showcase1" style="" /></div><?php endif; ?>

            <?php if($this->countModules('showcase2-1')) : ?><div id="showcase2">

            	<div id="showcase2-1"><jdoc:include type="modules" name="showcase2-1" style="xhtml" /></div><?php endif; ?>

                <?php if($this->countModules('showcase2-2')) : ?><div id="showcase2-2"><jdoc:include type="modules" name="showcase2-2" style="xhtml" /></div><?php endif; ?>

                <?php if($this->countModules('showcase2-3')) : ?><div id="showcase2-3"><jdoc:include type="modules" name="showcase2-3" style="xhtml" /></div>

            </div></div><?php endif; ?>

            

<?php if($this->countModules('intro')) : ?><div id="intro"><jdoc:include type="modules" name="intro" style="xhtml" /></div><?php endif; ?>



<?php if ($this->countModules( 'feature1 or feature2 or feature3 or feature4 or feature5 or feature6' )) : ?><div id="feature">

<?php if ($this->countModules('feature1')) {?>

<div id="feature1" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('feature2')) {?>

<div id="feature2" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('feature3')) {?>

<div id="feature3" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('feature4')) {?>

<div id="feature4" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('feature5')) {?>

<div id="feature5" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('feature6')) {?>

<div id="feature6" style="width:<?php echo $featuremodwidth ?>;" ><jdoc:include type="modules" name="feature6" style="xhtml" /> </div><?php } ?>

</div><?php endif; ?>

        

		<div id="maindiv">



<?php if ($this->countModules( 'maintop1 or maintop2 or maintop3 or maintop4 or maintop5 or maintop6' )) : ?><div id="maintop">

<?php if ($this->countModules('maintop1')) {?>

<div id="maintop1" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('maintop2')) {?>

<div id="maintop2" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('maintop3')) {?>

<div id="maintop3" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('maintop4')) {?>

<div id="maintop4" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('maintop5')) {?>

<div id="maintop5" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('maintop6')) {?>

<div id="maintop6" style="width:<?php echo $maintopmodwidth ?>;" ><jdoc:include type="modules" name="maintop6" style="xhtml" /> </div><?php } ?>

</div><?php endif; ?>



            <div id="maincontent">

            	<?php if($this->countModules('left')) : ?><div id="leftcol"><jdoc:include type="modules" name="left" style="xhtml" /></div><?php endif; ?>

                <div id="content<?php echo $contentwidth; ?>">

                	<?php if($this->countModules('contenttop')) : ?><div id="contenttop"><jdoc:include type="modules" name="contenttop" style="xhtml" /></div><?php endif; ?>

                    <!--Start Component-->

					<?php if (JRequest::getVar('view') != 'frontpage'): ?>

					<div id="component">

					<jdoc:include type="component" />

					</div>

					<?php endif ?>

               		<!--End Component-->

                    <?php if($this->countModules('contentbot')) : ?><div id="contentbot"><jdoc:include type="modules" name="contentbot" style="xhtml" /></div><?php endif; ?>

                </div>

                <?php if($this->countModules('right')) : ?><div id="rightcol"><jdoc:include type="modules" name="right" style="xhtml" /></div><?php endif; ?>

            </div>



<?php if ($this->countModules( 'mainbottom1 or mainbottom2 or mainbottom3 or mainbottom4 or mainbottom5 or mainbottom6' )) : ?><div id="mainbottom">

<?php if ($this->countModules('mainbottom1')) {?>

<div id="mainbottom1" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="mainbottom1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('mainbottom2')) {?>

<div id="mainbottom2" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="mainbottom2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('mainbottom3')) {?>

<div id="mainbottom3" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="mainbottom3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('mainbottom4')) {?>

<div id="mainbottom4" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="mainbottom4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('mainbottom5')) {?>

<div id="mainbottom5" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="mainbottom5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('mainbottom6')) {?>

<div id="mainbottom6" style="width:<?php echo $mainbottommodwidth ?>;" ><jdoc:include type="modules" name="maintop6" style="xhtml" /> </div><?php } ?>

</div><?php endif; ?>



        </div> 

        

 <?php if ($this->countModules( 'spotlight1 or spotlight2 or spotlight3 or spotlight4 or spotlight5 or spotlight6' )) : ?><div id="spotlight">

<?php if ($this->countModules('spotlight1')) {?>

<div id="spotlight1" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="spotlight1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('spotlight2')) {?>

<div id="spotlight2" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="spotlight2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('spotlight3')) {?>

<div id="spotlight3" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="spotlight3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('spotlight4')) {?>

<div id="spotlight4" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="spotlight4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('spotlight5')) {?>

<div id="spotlight5" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="spotlight5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('spotlight6')) {?>

<div id="spotlight6" style="width:<?php echo $spotlightmodwidth ?>;" ><jdoc:include type="modules" name="maintop6" style="xhtml" /> </div><?php } ?>

</div><?php endif; ?>



        </div>

        

<div id="botspacer"><?php if($this->countModules('botspacer')) : ?><jdoc:include type="modules" name="botspacer" style="xhtml" /><?php endif; ?></div>

    </div>

    </div>



<?php if ($this->countModules( 'bottom1 or bottom2 or bottom3 or bottom4 or bottom5 or bottom6' )) : ?><div id="bottomwrap"><div id="bottom">

<?php if ($this->countModules('bottom1')) {?>

<div id="bottom1" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="bottom1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('bottom2')) {?>

<div id="bottom2" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="bottom2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('bottom3')) {?>

<div id="bottom3" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="bottom3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('bottom4')) {?>

<div id="bottom4" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="bottom4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('bottom5')) {?>

<div id="bottom5" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="bottom5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('bottom6')) {?>

<div id="bottom6" style="width:<?php echo $bottommodwidth ?>;" ><jdoc:include type="modules" name="maintop6" style="xhtml" /> </div><?php } ?>

</div></div><?php endif; ?>

    

	<?php if ($this->countModules( 'footer1 or footer2 or footer3 or footer4 or footer5 or footer6' )) : ?><div id="footerwrap"><div id="footer">

<?php if ($this->countModules('footer1')) {?>

<div id="footer1" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer1" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('footer2')) {?>

<div id="footer2" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer2" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('footer3')) {?>

<div id="footer3" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer3" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('footer4')) {?>

<div id="footer4" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer4" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('footer5')) {?>

<div id="footer5" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer5" style="xhtml" /> </div><?php } ?>

<?php if ($this->countModules('footer6')) {?>

<div id="footer6" style="width:<?php echo $footermodwidth ?>;" ><jdoc:include type="modules" name="footer6" style="xhtml" /> </div><?php } ?>

</div></div><?php endif; ?>



	<div id="copydivwrap"><div id="copydiv">

    	<div id="copy1"><?php if($this->countModules('copy1')) : ?><jdoc:include type="modules" name="copy1" style="xhtml" /><?php endif; ?></div>

        <div id="copy2"><?php if($this->countModules('copy2')) : ?><jdoc:include type="modules" name="copy2" style="xhtml" /><?php endif; ?></div>

    </div></div>

</div>

</body>

</html>