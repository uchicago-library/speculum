<?php

/*
 * TEXT TOKENIZING FUNCTIONS
 */

/*
 * function to convert a string of text into 
 * an array of token chunks. This function needs to 
 * deal with quotes around exact search terms
 *
 * input:
 * search term, possibly in unicode.
 *
 * output:
 * array of tokens.
 * (, ), AND, OR, NOT will be strings.
 * any search term will itself be an
 * associative array. the key is the search node,
 * and the value is the search term. 
 */

function textTokenize($search, $node) {
	// uppercase, strip out unwanted chars.
	$s = cleanStringAlphaNumeric($search);
	// replace extra parens and quotes with whitespace.
	$s = normalizeParensAndQuotes($s);
	// split on whitespace, quotes and parens are tokens.
	$t = textTokenizeSub($s);
	// validateTokens
	$t = makeTokensExplicit($t, $node);
	return $t;
}

/* 
 * input:
 * string of chars from cleaned web form text input. 
 * output: 
 * array of tokens, each is a string. 
 */ 

function textTokenizeSub($s) {
	$chars = str_split($s);
	$mode = 'normal';
	$token = '';
	$tokens = array();

	foreach ($chars as $c) {
		switch ($mode) {
			case 'normal':
				if ($c == '"' || $c == "'") {
					if ($token != '') $tokens[] = $token;
					$token = '';
					$mode = $c;
				} else if ($c == ' ' || $c == "\t" || $c == "\n") {
					if ($token != '') $tokens[] = $token;
					$token = '';
				} else {
					$token .= $c;
				}
				break;
			default;
				if ($c == $mode) {
					if ($token != '') $tokens[] = $token;
					$token = '';
					$mode = 'normal';
				} else {
					$token .= $c;
				}
				break;
		}
	}
	if ($token != '') $tokens[] = $token;

	return $tokens;
}

function isBoolean($s) {
	return is_string($s) && ($s == 'AND' || $s == 'OR' || $s == 'NOT');
}
function isOpeningParen($s) {
	return is_string($s) && $s == '(';
}
function isClosingParen($s) {
	return is_string($s) && $s == ')';
}
function isWord($s) {
	return !(isBoolean($s) || isOpeningParen($s) || isClosingParen($s));
}
function node($t, $sn='') {
	if (isWord($t)) {
		return array($sn=>$t);
	} else {
		return $t;
	}
}

/*
 * input:
 * array of tokens (uppercase, no accents, quote aware, parens are
 * tokens. All tokens are text.)
 *
 * output:
 * validated string of tokens. implied 'AND' is spelled out. If a token
 * refers to a search term, then that token is a one element associative
 * array, where the key is the node to search and the value is the
 * search term. 
 */

function makeTokensExplicit($tokens, $sn) {
	$last = 'none';
	$out = array();
	foreach ($tokens as $t) {
		if ($last == 'none') {
			if (isWord($t)) { $out[] = node($t,$sn); }
			if (isBoolean($t) && $t == 'NOT') { $out[] = node('*',$sn); $out[] = node($t,$sn); }
			if (isOpeningParen($t)) { $out[] = node($t,$sn); }
			if (isClosingParen($t)) { continue; }
		} 
		if ($last == 'word') {
			if (isWord($t)) { $out[] = node('AND'); $out[] = node($t,$sn); }
			if (isBoolean($t)) { $out[] = node($t,$sn); }
			if (isOpeningParen($t)) { $out[] = node('AND'); $out[] = node($t,$sn); }
			if (isClosingParen($t)) { $out[] = node($t,$sn); }
		}
		if ($last == 'boolean') {
			if (isWord($t)) { $out[] = node($t,$sn); }
			if (isBoolean($t)) { array_pop($out); }
			if (isOpeningParen($t)) { $out[] = node($t,$sn); }
			if (isClosingParen($t)) { array_pop($out); $out[] = node($t,$sn); }
		}
		if ($last == 'openingparen') {
			if (isWord($t)) { $out[] = node($t,$sn); }
			if (isBoolean($t) && $t == 'NOT') { $out[] = node('*',$sn); $out[] = node($t,$sn); }
			if (isOpeningParen($t)) { $out[] = node($t,$sn); }
			if (isClosingParen($t)) { array_pop($out); }
		}
		if ($last == 'closingparen') {
			if (isWord($t)) { $out[] = node('AND'); $out[] = node($t,$sn); }
			if (isBoolean($t)) { $out[] = node($t,$sn); }
			if (isOpeningParen($t)) { $out[] = node('AND'); $out[] = node($t,$sn); }
			if (isClosingParen($t)) { $out[] = node($t,$sn); }
		}
		if (isWord($t)) 
			$last = 'word';
		if (isBoolean($t)) 
			$last = 'boolean';
		if (isOpeningParen($t)) 
			$last = 'openingparen';
		if (isClosingParen($t))
			$last = 'closingparen';
	}
	return $out;
}

function sameTopOpp($opps, $o) {
	return ($opps[count($opps)-1] == $o);
}

/*
 * convert linear string of tokens into AST.
 */

function makeAST($tokens) {

	$args = array();
	$opps = array();
	
	foreach ($tokens as $t) {
		if (isBoolean($t)) {
			if ($opps && !sameTopOpp($opps, $t)) {
				$newchunk = array();
				while (1) {
					if (!$args) {
						break;
					}
					$a = array_pop($args);
					if ($a == '(') {
						break;
					}
					array_unshift($newchunk, $a);
				}
				if ($opps) {
					array_unshift($newchunk, array_pop($opps));
				} else {
					array_unshift($newchunk, 'AND');
				}
				$args[] = $newchunk;
			}
			if ($opps && sameTopOpp($opps, $t)) {
				continue;
			}
			$opps[] = $t;
		} else {
			if (isClosingParen($t)) {
				$newchunk = array();
				while (1) {
					if (!$args) {
						break;
					}
					$a = array_pop($args);
					if ($a == '(') {
						break;
					}
					array_unshift($newchunk, $a);
				}
				if ($opps) {
					array_unshift($newchunk, array_pop($opps));
				} else {
					array_unshift($newchunk, 'AND');
				}
				$args[] = $newchunk;
			} else {
				$args[] = $t;
			}
		}
	}
	// CLOSE OUT AND RETURN
	$newchunk = array();
	while (1) {
		if (!$args) {
			break;
		}
		$a = array_pop($args);
		if ($a == '(') {
			break;
		}
		array_unshift($newchunk, $a);
	}
	if ($opps) {
		array_unshift($newchunk, array_pop($opps));
	} else {
		array_unshift($newchunk, 'AND');
	}
	return $newchunk;
}

/*
 * Helper functions for CLEANAST
 */

function isLeaf($a) {
	if (is_string($a)) 
		return True;
	if (count($a) == 1 && !array_key_exists(0, $a)) 
		return True;
	return False;
}

/*
 * CLEANAST removes redundant parenthesis and nesting.
 */

function cleanAST($ast) {
	if (isLeaf($ast)) {
		return $ast;
	}
	if ($ast[0] == 'AND' && count($ast) == 2) {
		return cleanAST($ast[1]);
	}
	$i = 0;
	while ($i < count($ast)) {
		$ast[$i] = cleanAST($ast[$i]);
		if (is_array($ast) && array_key_exists(0, $ast) && is_array($ast[0]) && array_key_exists(0, $ast[0]) && $ast[0] == $ast[$i][0]) {
			$a = 1;
			while ($a < count($ast[$i])) {
				$ast[] = $ast[$i][$a];
				$a = $a + 1;
			}
			array_splice($ast, $i, 1);
		}
		$i = $i + 1;
	}
	return $ast;
}

/*
 * Helper functions for EXECUTE SEARCH
 */

function isProcessLeaf($a) {
	if (!is_array($a)) 
		return False;
	if (count($a) != 1) 
		return False;
	$k = array_keys($a);
	if ($k[0] == ':::processed:::' || $k[0] == '')
		return False;
	return True;
}

function isBooleanBranch($a) {
	if (!is_array($a)) 
		return False;
	if (!array_key_exists(0, $a))
		return False;
	if (!isBoolean($a[0])) 
		return False;
	return True;
}

function termRequiresFullTextSearch($s) {
	if ($s == '*') 
		return False;
	if (strstr($s, ' '))
		return True;
	if (strstr($s, '?'))
		return True;
	if (strstr($s, '*'))
		return True;
	return False;
}

/*
 * EXECUTE SEARCH
 * input:
 * cleaned AST.
 * output:
 * a single 'AST node'. it will be a one element associative array. the
 * key will be ':::processed:::' and the value will be an array of
 * chicago numbers. 'A1', 'A100', etc. 
 */

function executeSearch($ast) {
	if (isProcessLeaf($ast)) {
		$k = array_keys($ast);
		$v = array_values($ast);
		$a = array();
		if ($k) {
			if (termRequiresFullTextSearch($v[0])) {
				$a[':::processed:::'] = exactStringSearch($v[0], $k[0]);
			} else {
				$a[':::processed:::'] = loadIndexData($k[0], $v[0]);
			}
			return $a;
		}
	}
	if (isBooleanBranch($ast)) {
		$i = 1;
		while ($i < count($ast)) {
			$ast[$i] = executeSearch($ast[$i]);
			$i++;
		}

		$tmp = array();
		foreach(array_slice($ast, 1) as $i) {
			$tmp[] = $i[':::processed:::'];
		}

		$a = array();
		$c = chiBoolean($ast[0], $tmp);
		$a[':::processed:::'] = $c;
		return $a;
	}
	return $ast;
}

/*
 * Helper function for processSearch.
 */

function tokensContainBoolean($tokens) {
	foreach ($tokens as $t) {
		if (isBoolean($t)) {
			return True;
		}
	}
	return False;
}

/*
 * SEARCH PROCESSING. 
 * INPUT: clean associative array of GET parameters.
 * OUTPUT: /findaid/work format finding aid. 
 */

function processSearch($clean) {

	// BUILD AN ARRAY OF SEARCH TERMS.
	$tokens = array();
	$i = 0;
	while ($i < 3 && isset($clean['search'][$i]) && $clean['search'][$i] != '') {
		if ($i > 0) {
			$tokens[] = cleanStringAlphaNumeric($clean['searchboolean'][$i]);
		}
		$t = textTokenize($clean['search'][$i], $clean['searchnode'][$i]);
		if (tokensContainBoolean($t)) {
			$tokens[] = '(';
		}
		$tokens = array_merge($tokens, $t);
		if (tokensContainBoolean($t)) {
			$tokens[] = ')';
		}
		$i++;
	}

	// BUILD AND CLEAN AST.
	$ast = makeAST($tokens);
	$ast = cleanAST($ast);

	// EXECUTE SEARCH.
	$ast = executeSearch($ast);

	// RETURN FINDING AID FROM ARRAY OF SPECULUM NUMBERS.
	return buildFindingAid($ast[':::processed:::']);
}

?>
