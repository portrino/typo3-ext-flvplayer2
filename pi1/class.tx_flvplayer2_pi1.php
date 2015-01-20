<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Jose Antonio Guerra <jaguerra@icti.es>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/** 
 * Plugin 'FLV Player' for the 'flvplayer2' extension.
 *
 * @author		Jean-David Gadina (macmade@gadlab.net)
 * @author		Jose Antonio Guerra <jaguerra@icti.es>
 */

class tx_flvplayer2_pi1 extends tslib_pibase {


		/***************************************************************
		 * SECTION 0 - VARIABLES
		 *
		 * Class variables for the plugin.
		 ***************************************************************/

		// Same as class name
		public $prefixId = 'tx_flvplayer2_pi1';

		// Path to this script relative to the extension dir
		public $scriptRelPath = 'pi1/class.tx_flvplayer2_pi1.php';

		// The extension key
		public $extKey = 'flvplayer2';

		// Upload directory
		public $uploadDir = 'uploads/tx_flvplayer/';


		/***************************************************************
		 * SECTION 1 - MAIN
		 *
		 * Functions for the initialization and the output of the plugin.
		 ***************************************************************/

		/**
		 * Returns the content object of the plugin.
		 * 
		 * This function initialises the plugin "tx_flvplayer2_pi1", and
		 * launches the needed functions to correctly display the plugin.
		 * 
		 * @param		$content			The content object
		 * @param		$conf				The TS setup
		 * @return		The content of the plugin.
		 * @see			setConfig
		 * @see			buildFlashCode
		 */
		public function main($content,$conf) {

				// Set class confArray TS from the function
				$this->conf = $conf;

				// Set final configuration (TS or FF)
				$this->setConfig();

				// Build content
				return $this->pi_wrapInBaseClass($this->processContent($content));
		}

		protected function processContent($content) {

				// Build content
				//

				$this->conf['url'] = $this->cObj->stdWrap($this->conf['url'], $this->conf['url.']);
				$this->conf['width'] = $this->cObj->stdWrap($this->conf['width'], $this->conf['width.']);
				$this->conf['height'] = $this->cObj->stdWrap($this->conf['height'], $this->conf['height.']);

				if ($this->conf['url'] && Tx_Flvplayer2_Helper_YouTube::isValidUrl($this->conf['url'])) {
						$content = $this->buildYouTubeCode();
				} elseif ($this->conf['url'] && strpos($this->conf['url'], 'vimeo.com')) {
						$content = $this->buildVimeoCode();
				} elseif ($this->conf['useFlowPlayer']) {
						$content = $this->buildFlashCodeFlowplayer();
				} else {
						$content = $this->buildFlashCode();
				}

				return $content;

		}

		/**
		 * Returns the content object of the plugin. 
		 * To be called from other extension.
		 * 
		 * @param		$conf				The TS setup (optional)
		 * @return		The content of the plugin.
		 */
		public function getVideoCode($videoUrl = NULL, $conf = NULL) {

				$this->cObj = t3lib_div::makeInstance('tslib_cObj');

				if (is_array($conf)) {
						$this->conf = $conf;
				}

				if (is_array($this->conf)) {
						$this->conf = t3lib_div::array_merge_recursive_overrule($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_flvplayer_pi1.'], $this->conf);
				} else {
						$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_flvplayer_pi1.'];
				}

				if ($videoUrl) {
						$this->conf['url'] = $videoUrl;
				}

				return $this->processContent('');
		}

		/**
		 * Set configuration array.
		 *
		 * This function is used to set the final configuration array of the
		 * plugin, by providing a mapping array between the TS & the flexform
		 * configuration.
		 *
		 * @return		Void
		 */
		protected function setConfig() {

				$this->pi_initPIflexForm();
				$piFlexForm = $this->cObj->data['pi_flexform'];

				// Mapping array for PI flexform
				$flex2conf = array(
						'url' => 'sDEF:url',
						'file' => 'sDEF:file',
						'image' => 'sDEF:image',
						'splashImageMode' => 'sDEF:splashImageMode',
						'playerParams.' => array(
								'autoStart' => 'sPLAYER:autostart',
								'fullScreen' => 'sPLAYER:fullscreen',
								'controlbar' => 'sPLAYER:controlbar',
						),
						'width' => 'sFLASH:width',
						'height' => 'sFLASH:height',
						'version' => 'sFLASH:version',
				);

				// Ovverride TS setup with flexform
				$this->conf = $this->fe_mergeTSconfFlex($flex2conf, $this->conf, $piFlexForm);

		}

		/**
		 * Merge plugin TS configuration with flexform configuration.
		 *
		 * This function merge the plugin TS configuration array with the
		 * flexform configuration (priority is given to flexform). Everything
		 * is done automatically with a mapping array containing the path of the
		 * TS elements to replace, and the path of the flexform fields in the XML.
		 *
		 * @param       array       $mapArray           The mapping array with informations about values to replace
		 * @param       array       $tsArray            The initial TS configuration array
		 * @param       string      $flexRes            The flexform object (usually $this->pObj->cObj->data[ 'pi_flexform' ])
		 * @return      array       The merged configuration array
		 */
		private function fe_mergeTSconfFlex( $mapArray, $tsArray, $flexRes )
		{
				if( !is_array($mapArray) || !is_array($tsArray) ) {
						return false;
				}
				$tempConfig = $tsArray;
				foreach( $mapArray as $key => $value ) {
						if( is_array( $value ) ) {
								$tempConfig[ $key ] = $this->fe_mergeTSconfFlex(
										$value,
										$tsArray[ $key ],
										$flexRes
								);
						} else {
								$flexInfo = explode( ':', $value );
								$flexValue = ( string )$this->pi_getFFvalue(
										$flexRes,
										$flexInfo[ 1 ],
										$flexInfo[ 0 ]
								);
								if( !empty( $flexValue ) || $flexValue == '0' ) {
										$tempConfig[ $key ] = $flexValue;
								}
						}
				}
				return $tempConfig;
		}

		/**
		 * Returns the code for the flash file.
		 * 
		 * This function creates a flash plugin object.
		 * 
		 * @return		The complete HTML code used to display the flash file.
		 * @see			writeFlashObjectParams
		 */
		protected function buildFlashCode() {

				// Autostart
				$autoStart = ($this->conf['playerParams.']['autoStart']) ? 'true' : 'false';

				// Allow fullscreen mode
				$fullScreen = ($this->conf['playerParams.']['fullScreen']) ? 'true' : 'false';

				if ($this->conf['url']) {
						$filePath =	$filePath =	$this->cObj->stdWrap($this->conf['url'], $this->conf['url.']);
				} else {
						// File path
						$filePath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace(PATH_site, '', t3lib_div::getFileAbsFileName($this->uploadDir . $this->conf['file']));
						$filePath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'), '', $filePath);
				}

				$extPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace(PATH_site, '', t3lib_extMgm::extPath('flvplayer2'));
				$extPath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'), '', $extPath);

				// Storage
				$htmlCode = array();

				// Include Adobe Flash Player Version Detection
				$GLOBALS['TSFE']->additionalHeaderData[$this->pi1->prefixId] = '<script type="text/JavaScript" src="' . t3lib_extMgm::siteRelPath("flvplayer2") . 'pi1/AC_OETags.js"></script>';

				// Allow <params> set from TS
				$paramsString = '';
				if (is_array($this->conf['swfParams.'])) {
						foreach ($this->conf['swfParams.'] as $name => $value) {
								$paramsString .= '"' . $name . '", "' . $value . '",';
						}
				}

				if ($this->conf['base']) {
						$base = $this->conf['base'];
				} else {
						$base = t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR');
				}

				// Create the flash stuff
				$htmlCode[] .= '
						<script type="text/javascript">
/*<![CDATA[*/
<!--
var hasRightVersion = DetectFlashVer(' . $this->conf['version'] . ', 0, 0);
if (hasRightVersion) {  // if we\'ve detected an acceptable version
		AC_FL_RunContent(
				"movie", "' . $extPath . 'pi1/mediaplayer",
				"width", "' . $this->conf['width'] . '",
				"height", "' . $this->conf['height'] . '",
				"quality", "high",
				"base", "' . $base . '",
				"flashvars","width=' . $this->conf['width'] .
				'&height=' . $this->conf['height'] .
				'&file=' . $filePath .
				'&autostart=' . $autoStart .
				'&image=' . $this->getSplashImageUrl() .
				'&controlbar=' . $this->conf['playerParams.']['controlbar'] .
				'&fullscreen=' . $fullScreen . '",
				"allowScriptAccess","always",
				"allowfullscreen","' . $fullScreen . '",
				"type", "application/x-shockwave-flash",
				"codebase", "http' . (t3lib_div::getIndpEnv('TYPO3_SSL')?'s':'') . '://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab",
				' . $paramsString . '
				"pluginspage", "http://www.adobe.com/go/getflashplayer",
				"wmode", "transparent"
		);
		} else {  // flash is too old or we can\'t detect the plugin
				document.write("You need to upgrade or install your Flash Player installation!");
		}
		// -->
		/*]]>*/
				</script>
				<noscript><p>You need to install Flash Player!</p></noscript>
			';

			// Return content
			return implode(chr(10), $htmlCode);
		}

		protected function buildFlashCodeFlowplayer() {

			$uid = $this->cObj->data['uid'];

			$jsUrl = t3lib_extMgm::siteRelPath('flvplayer2') . 'pi1/flowplayer-3.1.4.min.js';
			$swfUrl = t3lib_extMgm::siteRelPath('flvplayer2') . 'pi1/flowplayer-3.1.5.swf';
			$GLOBALS['TSFE']->additionalHeaderData['flvplayer2-flowplayer'] = '<script type="text/javascript" src="' . $jsUrl . '"></script>';

			/**
			 * Getting the config..
			 */
			// Autostart
			$autoStart = ($this->conf['playerParams.']['autoStart']) ? 'true' : 'false';

			if ($this->conf['url']) {
				$filePath =	$this->cObj->stdWrap($this->conf['url'], $this->conf['url.']);
			} else {
				// File path
				$filePath = t3lib_div::getIndpEnv('TYPO3_SITE_URL') . str_replace(PATH_site, '', t3lib_div::getFileAbsFileName($this->uploadDir . $this->conf['file']));
				$filePath = str_replace(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'), '', $filePath);
			}

			$autoBuffering = 'true';

			// Create the flash stuff
			if ($this->conf['image']) {
				$splashImgUrl = $this->getSplashImageUrl();
				$splashImg = '
						canvas: { 
								backgroundImage: "url(' . $splashImgUrl . ')",
								backgroundGradient: "none",
						}, 					
				';
				$autoBuffering = 'false';
			} else {
				$splashImg = '';
			}

			$this->conf['swfParams.']['src'] = $swfUrl;
			$flashParamsJSON = json_encode($this->conf['swfParams.']);

			$playerDomId = 'flowplayer-' . $uid . '-' . rand(1, 9999);

			$htmlCode[]  = '
			<a 
				 href="' . $filePath . '"  
				 style="display:block;width:' . $this->conf['width'] . 'px;height:' . $this->conf['height'] . 'px;"
				 id=' . $playerDomId . '">
			</a>
<script type="text/javascript">
/*<![CDATA[*/
flowplayer("' . $playerDomId . '", ' . $flashParamsJSON . ',  {
		' . $splashImg . '
				clip: {

						// these two configuration variables does the trick
						autoPlay: ' . $autoStart . ',
						autoBuffering: ' . $autoBuffering . ' // <- do not place a comma here   
		}

		});
		/*]]>*/	
		</script>';

			return implode(chr(10), $htmlCode);
		}


		protected function buildYouTubecode() {

			$uid = $this->cObj->data['uid'];

			// Wrong to iframe: https://www.youtube.com/watch?v=cX70vxPssAQ
			// OK: <iframe width="640" height="480" src="http://www.youtube.com/embed/cX70vxPssAQ" frameborder="0" allowfullscreen></iframe>
			$code = Tx_Flvplayer2_Helper_YouTube::getVideoIdFromUrl( $this->conf['url'] );
			$videoUrl = '//www.youtube.com/embed/' . $code;

			$htmlCode[] = '
				<iframe class="youtube-player" type="text/html" width="' . $this->conf['width'] . '" height="' . $this->conf['height'] . '" src="' . $videoUrl . '" frameborder="0"></iframe>			
			';
			return implode(chr(10), $htmlCode);
		}

		protected function buildVimeoCode() {

			$matches = array();
			preg_match('/vimeo.com\/([0-9]+)/', $this->conf['url'], $matches);
			if (!$matches[1]) {
				return '';
			}

			$videoCode = $matches[1];
			$uid = $this->cObj->data['uid'];
			$playerDomId = 'flvPlayer2Vimeo-' . $uid . '-' . rand(1,9999);

			$additionalParams = '';

			if ($this->conf['playerParams.']['autoStart']) {
					$additionalParams .= '&amp;autoplay=1';
			}

			$additionalParams .= '&amp;api=1';
			$additionalParams .= '&amp;player_id=' . $playerDomId;


			$htmlCode[] = '<iframe id="' . $playerDomId . '" src="http://player.vimeo.com/video/' . $videoCode . '?title=0&amp;byline=0&amp;portrait=0' . $additionalParams . '" width="' . $this->conf['width'] . '" height="' . $this->conf['height'] . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

			return implode(chr(10), $htmlCode);
		}


		protected function getSplashImageUrl(){

			$local_cObj = t3lib_div::makeInstance('tslib_cObj');

			if ($this->conf['splashImageMode']) {
				switch ($this->conf['splashImageMode']) {
					case 'resize2max' :
						$suf = 'm';
						break;
					case 'crop' :
						$suf = 'c';
						break;
					case 'resize' :
					default:
						$suf = '';
						break;
				}
			}

			$lConf = array(
				'image.' => array(
					'file' => 'uploads/tx_flvplayer/' . $this->conf['image'],
					'file.' => array(
						'width' => $this->conf['width'] . $suf,
						'height' => $this->conf['height']
					)

				)
			);

			return $local_cObj->IMG_RESOURCE($lConf['image.']);
		}

	}


?>