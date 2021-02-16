<?php
/**
 * @name		Page Builder CK
 * @package		com_pagebuilderck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
defined('_JEXEC') or die;

use Pagebuilderck\CKFof;
use Pagebuilderck\CKFramework;

require_once(PAGEBUILDERCK_PATH . '/helpers/defines.js.php');
$imagespath = PAGEBUILDERCK_MEDIA_URI . '/images/';

JHtml::_('jquery.framework');
CKFof::addStylesheet(PAGEBUILDERCK_MEDIA_URI . '/assets/ckbrowse.css');
CKFof::addScript(PAGEBUILDERCK_MEDIA_URI . '/assets/ckbrowse.js');

$returnFunc = $this->input->get('func', 'ckSelectFile', 'cmd');
$returnField = $this->input->get('field', $this->input->get('fieldid', '', 'string'), 'string');
$type = $this->input->get('type', 'image', 'string');
CKFramework::loadCss();

switch ($type) {
	case 'video' :
		$fileicon = 'file_video.png';
		break;
	case 'audio' :
		$fileicon = 'file_audio.png';
		break;
	case 'image' :
	default :
		$fileicon = 'file_image.png';
		break;
}
$fileTypes = array(
	'video' => array('.mp4', '.ogv', '.webm', '.MP4', '.OGV', '.WEBM')
	, 'audio' => array('.mp3', '.ogg', '.MP3', '.OGG')
	, 'pdf' => array('.pdf')
	, 'image' => array('.jpg', '.jpeg', '.png', '.gif', '.tiff', '.JPG', '.JPEG', '.PNG', '.GIF', '.TIFF', '.ico', '.svg')
);
?>
<div id="ckbrowse" class="clearfix">
	<div id="ckfolderupload">
		<div class="inner">
			<div class="upload">
				<h2 class="uploadinstructions"><?php echo JText::_('Drop files here to upload'); ?></h2>
				<p><?php echo JText::_('or Select Files'); ?></p><input id="ckfileupload" type="file" name="Filedata[]" multiple class="" />
			</div>
		</div>
	</div>
	<div id="ckfoldertreelist">
		<p><?php echo JText::_('CK_BROWSE_INFOS') ?></p>
		<h3><?php echo JText::_('CK_FOLDERS') ?></h3>
<?php
$lastitem = 0;
foreach ($this->items as $i => $folder) {
	$submenustyle = '';
	$folderclass = '';
	if ($folder->level == 1) {
		$submenustyle = 'display: block;';
		$folderclass = 'ckcurrent';
	}
	?>
			<div class="ckfoldertree <?php echo $folderclass ?> <?php echo ($folder->deeper ? 'parent' : '') ?> <?php echo (count($folder->files) ? 'hasfiles' : '') ?>" data-level="<?php echo $folder->level ?>" data-path="<?php echo utf8_encode($folder->basepath) ?>">
			<?php if ($folder->level > 1) { ?><div class="ckfoldertreetoggler" onclick="ckBrowseToggleTreeSub(this)"></div><?php } ?>
				<div class="ckfoldertreename" onclick="ckBrowseShowFiles(this)"><span class="icon-folder"></span><?php echo utf8_encode($folder->name); ?>
					<div class="ckfoldertreecount"><?php echo count($folder->files); ?></div>
				</div>
				<div class="ckfoldertreefiles">
					<div class="ckfoldertreepathway ckinterface">
						<span><?php echo str_replace('/', '</span><span class="ckfoldertreepath">', utf8_encode($folder->basepath)) ?></span>
	<?php
	if (CKFof::userCan('create', 'com_media')) {
		?>
							<span class="ckfoldertreepathwayactions">
								<span class="ckfoldertreepathwayaddfolder ckbutton" onclick="ckAddFolder()"><?php echo JText::_('CK_ADD_SUB_FOLDER') ?></span>
								<span class="ckfoldertreepathwayfoldername"><input type="text" class="ckfoldertreepathwayaddfoldername" /></span>
								<span class="ckfoldertreepathwaycreatefolder ckbutton" onclick="ckCreateFolder(this, '<?php echo utf8_encode($folder->basepath) ?>')"><?php echo JText::_('CK_CREATE_FOLDER') ?></span>
							</span>
	<?php } ?>
					</div>
						<?php
						foreach ($folder->files as $j => $file) {
							$ext = JFile::getExt($file);
							$fileType = 'generic';
							foreach ($fileTypes as $type => $extsArray) {
								if (in_array('.' . $ext, $extsArray))
									$fileType = $type;
							}
							?>
						<div class="ckfoldertreefile ckwait" data-type="<?php echo $fileType ?>" onclick="ckBrowseSelectFile(this)" data-path="<?php echo utf8_encode($folder->basepath) ?>" data-filename="<?php echo utf8_encode($file) ?>">
							<div class="ckfakeimage" data-src="<?php echo JUri::root(true) . '/' . utf8_encode($folder->basepath) . '/' . utf8_encode($file) ?>" title="<?php echo utf8_encode($file); ?>" ></div>
							<div class="ckimagetitle"><?php echo utf8_encode($file); ?></div>
						</div>
					<?php } ?>
				</div>

				<?php
				if ($folder->deeper) {
					echo '<div class="cksubfolder" style="' . $submenustyle . '">';
				} elseif ($folder->shallower) {
					// The next item is shallower.
					echo '</div>'; // close ckfoldertree
					echo str_repeat('</div></div>', $folder->level_diff); // close cksubfolder + ckfoldertree
				} else {
					// The next item is on the same level.
					echo '</div>'; // close ckfoldertree
				}
			}
			?>
		</div>
		<div id="ckfoldertreepreview">
			<div class="inner">
			<?php if ($type == 'image') { ?>
					<div id="ckfoldertreepreviewimage">
					</div>
<?php } ?>
			</div>
		</div>

	</div>
	<script>
		function ckBrowseToggleTreeSub(btn) {
			var item = $ck(btn).parent();
			if (item.hasClass('ckopened')) {
				item.removeClass('ckopened');
			} else {
				item.addClass('ckopened')
				// item.find('> .cksubfolder, > .ckfoldertreefiles').css('opacity','0').animate({'opacity': '1'}, 300);
			}
		}

		function ckBrowseShowFiles(btn) {
			// show the image in place of divs
			var fakeImages = $ck(btn).find('~ .ckfoldertreefiles .ckfakeimage');
			if (fakeImages.length) {
				fakeImages.each(function () {
					$fakeImage = $ck(this);
					var source = $fakeImage.parent().attr('data-type') == 'image' ? $fakeImage.attr('data-src') : '<?php echo $imagespath . 'file_' ?>' + $fakeImage.parent().attr('data-type') + '.png';
					$fakeImage.after('<img src="' + source + '" title="' + $fakeImage.attr('title') + '" />');
					$fakeImage.parent().removeClass('ckwait');
					$fakeImage.remove();
				});
			}
			// set the current state on the folder
			var item = $ck(btn).parent();
			$ck('.ckcurrent').not(btn).removeClass('ckcurrent');
			if (item.hasClass('ckcurrent')) {
				item.removeClass('ckcurrent');
			} else {
				item.addClass('ckcurrent')
			}
		}

		function ckBrowseSelectFile(btn) {
			try {
				if (typeof (window.parent.<?php echo $returnFunc ?>) != 'undefined') {
					window.parent.<?php echo $returnFunc ?>($ck(btn).attr('data-path') + '/' + $ck(btn).attr('data-filename'), '<?php echo $returnField ?>');
					if (typeof (window.parent.CKBox) != 'undefined')
						window.parent.CKBox.close();
				} else {
					alert('ERROR : The function <?php echo $returnFunc ?> is missing in the parent window. Please contact the developer');
				}
			} catch (err) {
				alert('ERROR : ' + err.message + '. Please contact the developper.');
			}
		}

	</script>
