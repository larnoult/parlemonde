<?php

/*
 * Created on 23.04.2008, Redefined on 08.09.2012 by Tom
 * @author Thilo Kaindl
 */
class WordSearch {
	/** dimensions */
	private $v;
	private $h;

	/** grid place holder char */
	private $gridFiller = '-';

	private $upperChar = true;
        private $language;

	public $grid = array ();

	public $words;
        public $wordchars;
	public $problemWords;

	public $wordList;

	function __construct($words, $dim) {
            //load wordlist
            mb_internal_encoding("UTF-8");
            $this->v = $dim;
            $this->h = $dim;
            $this->wordchars = strtoupper(str_replace(',', '', $words));
            
            $this->wordList = split(',', $words);
            
            //$this->initWordList();
            $this->initGrid();
            
            //$wordList = $this->wordList[$theme];
            for ($i = 0; $i < count($this->wordList); $i++) {
                    $try1 = $this->insertRandom($this->wordList[$i]);
                    if (!$try1) {
                            $try2 = $this->insertAnyHow($this->wordList[$i]);
                    }

                    if (!$try1 && !$try2) {
                            $this->setProblemWord($this->wordList[$i]);
                    }

            }
            
            $this->fillReadyGridWithChars();
	}

	private function initGrid() {
		for ($v = 0; $v < $this->v; $v++) {
			for ($h = 0; $h < $this->h; $h++) {
				$this->grid[$v][$h] = $this->gridFiller;
			}
		}
	}
        
        private function utf8_strrev($str){
            preg_match_all('/./us', $str, $ar);
            return join('',array_reverse($ar[0]));
        }

	private function insert($word, $direction, $reverse = false) {
		if ($this->upperChar == true) {
			$word = strtoupper($word);
			$saveWord = $word;
		}
		if ($reverse) {
			//$word = strrev($word);
                    $word = $this->utf8_strrev($word);
		}
                
		if ($direction == 'h') {
			if (mb_strlen($word) <= $this->h) {
				$success = $this->tryToInsert($word, $direction);
			}
		}
		if ($direction == 'v') {
			if (mb_strlen($word) <= $this->v) {
				$success = $this->tryToInsert($word, $direction);
			}
		}
		if ($direction == 'd' || $direction == 'd-') {
			if (mb_strlen($word) <= $this->h || mb_strlen($word) <= $this->v) {
				$success = $this->tryToInsert($word, $direction);

			}
		}
		if ($success) {
			$this->setValidWord($saveWord);
			return true;
		}
		return false;
	}

	private function insertAnyHow($word) {
		if ($this->upperChar == true) {
			$word = strtoupper($word);
		}
		$directions = array (
			'h',
			'v',
			'd',
			'd-'
		);
		$reverse = array (
			false,
			true
		);
		for ($i = 0; $i < count($directions); $i++) {
			if ($this->insert($word, $directions[$i], false)) {
				return true;
			}
		}
		for ($i = 0; $i < count($directions); $i++) {
			if ($this->insert($word, $directions[$i], true)) {
				return true;
			}
		}
		return false;
	}

	private function insertRandom($word) {
		if ($this->upperChar == true) {
			$word = strtoupper($word);
		}
		$randDirection = rand(0, 3);
		$directions = array (
			'h',
			'v',
			'd',
			'd-'
		);
		$direction = $directions[$randDirection];
		$randReverse = rand(1, 2);
		if ($randReverse % 2 == 0) {
			$reverse = true;
		} else {
			$reverse = false;
		}
		return $this->insert($word, $direction, $reverse);
	}

	private function tryToInsert($word, $direction) {
		$startPoints = $this->getPossibleStartsPoints($word, $direction);
		for ($i = 0; $i < count($startPoints); $i++) {
			$v = $startPoints[$i]['v'];
			$h = $startPoints[$i]['h'];
			if ($this->isFillableBlock($v, $h, $word, $direction)) {
				$this->insertWord($word, $v, $h, $direction);
				return true;
			}
		}
		return false;
	}

	private function insertWord($word, $v, $h, $direction = 'h') {
		for ($i = 0; $i < mb_strlen($word); $i++) {
			$this->grid[$v][$h] = mb_substr($word, $i, 1);
			if ($direction == 'h') {
				$h++;
			}
			if ($direction == 'v') {
				$v++;
			}
			if ($direction == 'd') {
				$v++;
				$h++;
			}
			if ($direction == 'd-') {
				$v--;
				$h++;
			}
		}
	}

	private function getPossibleStartsPoints($word, $direction) {
		if ($direction == 'h') {
			$maxV = $this->v;
			$maxH = $this->h - mb_strlen($word) + 1;
		}
		if ($direction == 'v') {
			$maxV = $this->v - mb_strlen($word) + 1;
			$maxH = $this->h;
		}
		if ($direction == 'd') {
			$maxV = $this->v - mb_strlen($word) + 1;
			$maxH = $this->h - mb_strlen($word) + 1;
		}

		if ($direction == 'd-') {
			$maxV = $this->v;
			$maxH = $this->h - mb_strlen($word) + 1;
		}

		if ($direction == 'd-') {
			for ($v = mb_strlen($word) - 1; $v < $maxV; $v++) {
				for ($h = 0; $h < $maxH; $h++) {
					$startPoints[] = array (
						'v' => $v,
						'h' => $h
					);
				}
			}
		} else {

			for ($v = 0; $v < $maxV; $v++) {
				for ($h = 0; $h < $maxH; $h++) {
					$startPoints[] = array (
						'v' => $v,
						'h' => $h
					);
				}
			}
		}
		shuffle($startPoints);
		return $startPoints;
	}

	private function isFillableBlock($v, $h, $word, $direction) {
		$length = mb_strlen($word);
		for ($i = 0; $i < $length; $i++) {
			$myChar = mb_substr($word, $i, 1);
			if ($this->grid[$v][$h] == $this->gridFiller || $this->grid[$v][$h] == $myChar) {
				if ($direction == 'h') {
					$h++;
				}
				if ($direction == 'v') {
					$v++;
				}
				if ($direction == 'd') {
					$h++;
					$v++;
				}
				if ($direction == 'd-') {
					$h++;
					$v--;
				}
			} else {
				return false;
			}

		}
		return true;
	}

	private function setProblemWord($word) {
		$this->problemWords[] = $word;
	}

	private function setValidWord($word) {
		$this->words[] = $word;
	}

	private function fillReadyGridWithChars() {
		for ($v = 0; $v < $this->v; $v++) {
			for ($h = 0; $h < $this->h; $h++) {
				if ($this->grid[$v][$h] == $this->gridFiller)
					$this->grid[$v][$h] = $this->getRandomChar();
			}
		}
	}

	public function printGrid() {
		for ($v = 0; $v < $this->v; $v++) {
			for ($h = 0; $h < $this->h; $h++) {
				echo $this->grid[$v][$h];
				echo ' ';
			}
			echo '<br>';
		}
	}

	public function getGridFlashData($type) {
		$flashData = "&title=" . ucfirst($type);
		for ($v = 0; $v < $this->v; $v++) {
			$flashData .= "&row" . ($v +1) . "=";
			for ($h = 0; $h < $this->h; $h++) {
				$flashData .= $this->grid[$v][$h];
			}
		}
                
		sort($this->words);

		$flashData .= "&numwords=" . count($this->words);

		for ($i = 0; $i < count($this->words); $i++) {
			$flashData .= "&word" . ($i +1) . "=" . $this->words[$i];
		}
		$flashData .= "&";
		return $flashData;
	}
        public function getGridData() {
		$wsData = "";
		for ($v = 0; $v < $this->v; $v++) {
			$wsData .= "&row" . ($v +1) . "=";
			for ($h = 0; $h < $this->h; $h++) {
                            if ($this->grid[$v][$h] == ' ') {
                                $this->grid[$v][$h] = $this->getRandomChar();
                            }
                            $wsData .= $this->grid[$v][$h];
			}
		}
                //echo(utf8_encode($wsData));die();
                sort($this->words);

		$wsData .= "&numwords=" . count($this->words);

		for ($i = 0; $i < count($this->words); $i++) {
			$wsData .= "&word" . ($i +1) . "=" . $this->words[$i];
		}
		$wsData .= "&";
                
		return $wsData;
	}
        
	public function getGrid() {
		$this->initGrid();
		return $this->grid;
	}

	private function getRandomChar() {
		
            $maxNumber = mb_strlen($this->wordchars);
            if ($maxNumber > 0) {
                    $randomNumber = rand(0, $maxNumber -1);
                    return mb_substr($this->wordchars, $randomNumber, 1);
            }
            return false;
	}
        
        public function save_printed($iDim) {
		$myDatabase = new Database();

		$sqlQuery = "insert into puzzles_printed (game, level, design)";
		$sqlQuery .= " values ('wordsearch', ".$iDim.", 0)" ;
		return($myDatabase->sendQuery($sqlQuery));
		
	}

}
?>