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
 * Helper class to handle YouTube URLs
 *
 * @package TYPO3
 * @subpackage tx_flvplayer2
 *
 */
class Tx_Flvplayer2_Helper_YouTubeTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

		/**
		 * @test
		 * @return void
		 */
		public function checkIsYouTubeUrl() {
				$this->assertTrue( Tx_Flvplayer2_Helper_YouTube::isValidUrl( 'http://www.youtube.com/watch?v=cX70vxPssAQ' ) );
				$this->assertTrue( Tx_Flvplayer2_Helper_YouTube::isValidUrl( 'http://youtu.be/cX70vxPssAQ' ) );
				$this->assertTrue( Tx_Flvplayer2_Helper_YouTube::isValidUrl( 'https://www.youtube.com/watch?v=cX70vxPssAQ' ) );
				$this->assertTrue( Tx_Flvplayer2_Helper_YouTube::isValidUrl( 'https://youtu.be/cX70vxPssAQ' ) );
				$this->assertTrue( Tx_Flvplayer2_Helper_YouTube::isValidUrl( '//www.youtube.com/embed/cX70vxPssAQ' ) );
		}

		/**
		 * @test
		 * @return void
		 */
		public function getVideoIdFromUrl() {
				$this->assertEquals('cX70vxPssAQ', Tx_Flvplayer2_Helper_YouTube::getVideoIdFromUrl( 'http://www.youtube.com/watch?v=cX70vxPssAQ' ) );
				$this->assertEquals('cX70vxPssAQ', Tx_Flvplayer2_Helper_YouTube::getVideoIdFromUrl( 'http://youtu.be/cX70vxPssAQ' ) );
				$this->assertEquals('cX70vxPssAQ', Tx_Flvplayer2_Helper_YouTube::getVideoIdFromUrl( '//www.youtube.com/embed/cX70vxPssAQ' ) );
		}

		/**
		 * @test
		 * @return void
		 */
		public function getVideoIdFromUrlReturnsFalseOnUnknown() {
				$this->assertFalse(Tx_Flvplayer2_Helper_YouTube::getVideoIdFromUrl( 'cX70vxPssAQ' ) );
		}
}

?>