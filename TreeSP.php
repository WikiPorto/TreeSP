<?php
/**
 * TreeSP extension - Adds #treesp parser functions to be used with Extension:TreeAndMenu
 * http://www.mediawiki.org/wiki/Extension:TreeAndMenu
 * 
 * @file
 * @ingroup Extensions
 * @author Alexandre Porto da Silva
 * @licence GNU General Public Licence 3.0 or later
 * @source https://github.com/WikiPorto/TreeSP
 * @version_1.0.0 2012-12-15
 * @version_1.1.0 2024-05-16
 * @version_1.2.0 2024-05-19
 */

# https://www.mediawiki.org/wiki/Manual:MediaWikiServices.php
use MediaWiki\MediaWikiServices;
# https://www.mediawiki.org/wiki/Manual:Title.php
use MediaWiki\Title\Title;

class TreeSP
{
	public static $instance = null;

	private $dbKey = '', $prefix = '';
	private $pattern = '|^(.+)/([^/]+)$|';
	
	/**
	 * Called when the extension is first loaded
	 */
	public static function onRegistration() {
		global $wgExtensionFunctions;
		self::$instance = new self();
		$wgExtensionFunctions[] = [ self::$instance, 'setup' ];
	}

	/**
	 * Called at extension setup time, install hooks and module resources
	 */
	public function setup() {
		// Add parser hooks
		$parser = MediaWikiServices::getInstance()->getParser();
		$parser->setFunctionHook( 'treesp', [ $this, 'renderTreeSP' ] );
	}

	public function renderTreeSP( $parser, $root = '' ) {
		$title = preg_replace( '(^[^\[]*\[+|[\|\]].*$)', '', $root );
		$objTitle = "$title" == '' ? $parser->getTitle() : Title::newFromText( $title );
		$ns = $objTitle->getNamespace();
		$this->dbKey = $objTitle->getDBKey();

		$objDBR = MediaWikiServices::getInstance()->getDBLoadBalancerFactory()->getReplicaDatabase();
		$objRS = $objDBR->newSelectQueryBuilder()
			->select( 'page_title' )
			->from( 'page' )
			->where( [ 'page_is_redirect' => 0, 'page_namespace' => $ns, "page_title LIKE '$this->dbKey/%'" ] )
			->orderBy( 'page_title' )
			->caller( __METHOD__ )
			->fetchResultSet();
		if ( $objRS ) {
			$this->prefix = '';
			if ( $ns != 0 && preg_match( '/^([^:]+:)/', $objTitle->getPrefixedText(), $match ) ) {
				$this->prefix = $match[1];
			}
			$list = $this->getList( $objRS );
			if ( $list ) {
				$objTreeAndMenu = new TreeAndMenu;
				$text = $objTitle->getText();
				if ( $text == '%' ) {
					return $objTreeAndMenu->expandTree( $parser, $list );
				} else {
					if ( !preg_match( '/^\[\[([^\[\]]+)]]$/', $root ) ) {
						$root = "[[$this->prefix$text]]";
					}
					return $objTreeAndMenu->expandTree( $parser, "root=$root", $list );
				}
			}
		}
		return '';
	}

	private function getList( $objRS ) {
		$list = $base = array();
		foreach ( $objRS as $row ) {
			$key = $row->page_title;
			while ( preg_match( $this->pattern, $key, $match ) ) {
				$key = $match[1];
				$base[$key] = true;
				if ( $key == $this->dbKey ) {
					break;
				}
				$list[$key] = $this->getLink( $key );
			}
		}
		foreach ( $objRS as $row ) {
			$title = $row->page_title;
			if ( !isset( $base[$title] ) ) {
				$key = preg_replace( $this->pattern, '\1/~~~\2', $title );
				$list[$key] = $this->getLink( $title );
			}
		}
		ksort( $list );
		return $list ? implode( "\n", $list ) : '';
	}

	private function getLink( $title ) {
		$piped = $text = preg_replace( '/_/', ' ', $title );
		if ( preg_match( $this->pattern, $piped, $match ) ) {
			$piped = $match[2];
		}
		$levels = preg_replace( array("|^$this->dbKey/|", '|[^/]+/|', '/[^*]+$/'), array('','*','*'), $title );
		return "$levels [[$this->prefix$text|$piped]]";
	}
}
