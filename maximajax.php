<?php
//-- maximajax.php
//--
//-- Copyright (c) 2016 Junichi Fujimori (fuzzy_jazzy@Wacooky.com)
//-- Released under the MIT license
//-- http://opensource.org/licenses/mit-license.php
//--
//-- Ver. 1.0: 2016-03-10 GitHub
//--
//--------------------------------------------------------------
	require_once( dirname(__FILE__) . '/environment.php' );
//-- environment.php provides following variables:
//-- $ROOT_URL 		:http://xxxx
//-- $MODULE_URL	:http://xxxx/yyy/this.php
//-- $JQUERY_URL
//-- $MATHJAX_URL
//-- $MAXIMA_URL
//--
//-- MAXIMA_CMD
//--------------------------------------------------------------
	$query = $_SERVER['QUERY_STRING'];
	$_SERVER['QUERY_STRING'] = "";

	if ( isset( $_POST['json'] ) ) {
		$json = $_POST['json'];
		$lines = array();

		if ( $json != '' ) {
			$assoc = json_decode( $json, TRUE);
			$maxima = new Maxima();
			$maxima->render_tex(TRUE);
			//$lines = $maxima->batch( $cmds );
			$lines = $maxima->pipe( $assoc['cmds'] );
		}

		$data = array();
		$data['result'] = $lines;
		echo json_encode( $data );
		exit();
	}

	class ExternalProcess {
		protected $outstr;
		protected $errstr;
		protected $newline = "\n";

		public function get_raw_out() {
			return $this->outstr;
		}

		public function get_raw_err() {
			return $this->errstr;
		}

		public function get_out() {
			return explode( $this->newline, $this->outstr);
		}

		public function get_err() {
			return explode( $this->newline, $this->errstr);
		}

		public function exec( $cmd ) {
			$desc = array(
				0 => array("pipe", "r"), //-- strin
				1 => array("pipe", "w"), //-- strout
				2 => array("pipe", "w")  //-- stredd
				//2 => array("file", "/dev/null", "w")
			);
			$closed = array(
				0 => FALSE,
				1 => FALSE,
				2 => FALSE
			);
			$available = array(
				0 => FALSE,
				1 => FALSE,
				2 => FALSE
			);
			$this->outstr = '';
			$this->errstr = '';
			$ms = 0; //sleep time in msec

			$cwd = NULL;
			$env = NULL;
			$process = proc_open($cmd, $desc, $pipes, $cwd, $env);
			if ($process === FALSE)
				return -1; //-- error1

//			$available[0] = stream_set_write_buffer($pipes[0], 0) !== 0; //-- sdtin no buffering
			$available[0] = !stream_set_blocking($pipes[0], FALSE); //-- stdout nonblocking
			$available[1] = !stream_set_blocking($pipes[1], FALSE); //-- stdout nonblocking
			$available[2] = !stream_set_blocking($pipes[2], FALSE); //-- stderr nonbloking
/*
			echo ($available[0] ? 'TRUE' : 'FALSE') . "\n";
			echo ($available[1] ? 'TRUE' : 'FALSE') . "\n";
			echo ($available[2] ? 'TRUE' : 'FALSE') . "\n";
*/

			if ($available[0] || $available[1] || $available[2] ) {
				fclose($pipes[0]);
				fclose($pipes[1]);
				fclose($pipes[2]);
				return proc_close( $proccess ); //-- -1: ERROR
			}

			while ( ($status = proc_get_status($process) !== FALSE)
							&& $status['running'] != FLASE ) {
				//-- std1out
				$available[1] = FALSE;
				if (!$closed[1]) {
					if (feof($pipes[1])) {
						fclose($pipes[1]);
						$closed[1] = TRUE;
					} else {
						$str = fgets($pipes[1], 1024);
						if (($len = strlen($str))) {
							$available[1] = TRUE;
							$this->outstr .= $str;
						}
					}
				}
				//-- stderr
				$available[2] = FALSE;
				if (!$closed[2]) {
					if (feof($pipes[2])) {
						fclose($pipes[2]);
						$closed[2] = TRUE;
					} else {
						$str = fgets($pipes[2], 1024);
						if (($len = strlen($str))) {
							$available[2] = TRUE;
							$this->errstr .= $str;
						}
					}
				}
				if ($closed[1] && $closed[2])
					break; //-- out/err pipe closed

				//-- sleep?
				if (!$available[1]  && !$available[2]) {
					//echo "$ms\n";
					if ($ms > 200) {
						//-- may hung up
						fclose($pipes[0]);
						fclose($pipes[1]);
						fclose($pipes[2]);
						$closed[0] = TRUE;
						$closed[1] = TRUE;
						$closed[2] = TRUE;
						proc_terminate($process);
						continue;
					}
					$ms += 10; //-- msec
					usleep($ms * 1000); // sleep for $ms milliseconds
					continue;
				}
				$ms = 0;
			}

			if (!$closed[0])
				fclose($pipes[0]);
			return proc_close( $proccess ); //-- -1: ERROR
		}
	}

	class Maxima {
		protected $show_header = TRUE;
		protected $render_tex = FALSE;

		public function show_header() {
			if (func_num_args() == 0)
				return $this->show_header;
			else
				$this->show_header = func_get_arg(0);
		}

		public function render_tex() {
			if (func_num_args() == 0)
				return $this->render_tex;
			else
				$this->render_tex = func_get_arg(0);
		}

		public function pipe( $cmds ) {
			$cmd = $this->batch_command($cmds);
			$process = new ExternalProcess();
			$process->exec( $cmd );
			$lines = $process->get_out();
			$lines = $this->html_output( $lines );
			$lines[] = ''; //-- for last '\n'
			return $lines;
		}

		protected function batch_command( $cmds ) {
			//--  double quotation must be escaped
			//$source = 'load(\"mactex-utilities.lisp\")$' . "\n";
			$source = '';
			$source .= implode("", $cmds);
			$arg = '--batch-string="' . $source . '"';
			$cmd = MAXIMA_CMD . ' ' . $arg;
			return $cmd;
		}

		public function batch( $cmds ) {
			$cmd = $this->batch_command($cmds);
			$lines = [];
			$status = -1;
			exec( $cmd, $lines, $status);
			$lines = $this->html_output( $lines );
			$lines[] = ''; //-- for last '\n'
			return $lines;
		}

		protected function html_output( &$lines ) {
			$i = 0;
			$html = [];
			$pre = FALSE; //-- if currnnt line is in <pre>

			//-- header message
			if ($this->show_header ) {
				$pre = TRUE;
				$html[] = '<pre>';
			}
			$line = $lines[$i++];
			while( $line !== NULL && strpos($line, '(%') !== 0 ) {
				if ($this->show_header )
					$html[] = $line;
				$line = $lines[$i++];
			}
			$i--;

			//-- (%i1), (%o1) ...
			//$this->tex = TRUE;
			if (!$pre ) {
				$pre = TRUE;
				$html[] .= '<pre>';
			}
			while( ($line = $lines[$i++]) !== NULL ) {
				//-- begin woth '$$' ?
				if ( $this->render_tex && strpos($line, '$$') === 0 ) {
						if ( $pre ) {
							$html[]= '</pre>';
							$pre = FALSE;
						}

						$line = $this->process_tex($line);
						$html[] = $line;
						//-- the same line ends with '$$' ?
						if ( strpos($line, '$$', 2) > 0 ) {
							$html[] = '<pre>';
							$pre = TRUE;
						}
						continue;
				}
				//-- end with '$$' ?
				if ( $this->render_tex && strpos($line, '$$') > 0 ) {
						$line = $this->process_tex($line);
						$html[] = $line;
						$html[] = '<pre>';
						$pre = TRUE;
						continue;
				}

				if ($this->render_tex && !$pre)
					$line = $this->process_tex($line);

				//-- output color
				$line = preg_replace('/^(\(\%i\d+\))/', '<span class="maxima-in">$1</span>', $line);
				$line = preg_replace('/^(\(\%o\d+\))/', '<span class="maxima-out">$1</span>', $line);
				$html[] = $line;
			}

			if ( $pre )
				$html[] = '</pre>';
			return $html;
		}

		protected function process_tex($line) {
			//-- Greek letter '{\it \%xxx}' --> \xxx
			$line = preg_replace('/\{\\\it\s*\\\\%(\w+)\}/', '\\\$1', $line);
			//-- 'leqno{\tt (%xx)}' --> 'tag{%xx}''
			$line = preg_replace('/leqno\{\\\tt\s*\(\\\(.*)\)\}/', 'tag{$1}', $line);
			return $line;
		}

	}
?>
