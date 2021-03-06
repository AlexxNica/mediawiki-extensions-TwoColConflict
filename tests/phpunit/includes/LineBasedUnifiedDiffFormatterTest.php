<?php

/**
 * @license GNU GPL v2+
 * @author Christoph Jauera <christoph.jauera@wikimedia.de>
 */
class LineBasedUnifiedDiffFormatterTest extends MediaWikiTestCase {

	/**
	 * @param string $before
	 * @param string $after
	 * @param array $expectedOutput
	 * @dataProvider provider_testFormat
	 * @covers LineBasedUnifiedDiffFormatter::format
	 */
	public function testFormat( $before, $after, $expectedOutput ) {
		$diff = new Diff( explode( "\n", $before ), explode( "\n", $after ) );
		$instance = new LineBasedUnifiedDiffFormatter();
		$output = $instance->format( $diff );
		$this->assertEquals( $expectedOutput, $output );
	}

	public function provider_testFormat() {
		return [
			[
				'before' => 'Just text.',
				'after' => 'Just text.',
				'result' => [
					1 => [
						[
							'action' => 'copy',
							'copy' => 'Just text.',
							'oldline' => 1,
							'newline' => 1
						]
					]
				],
			],
			[
				'before' => 'Just text.',
				'after' => 'Just text. And more.',
				'result' =>
					[
						1 =>
							[
								[
									'action' => 'delete',
									'old' => 'Just text.',
									'oldline' => 1
								],
								[
									'action' => 'add',
									'new' => 'Just text<ins class="diffchange">. And more</ins>.',
									'newline' => 1
								]
							],
					],
			],
			[
				'before' => 'Just less text.',
				'after' => 'Just less.',
				'result' =>
					[
						1 =>
							[
								[
									'action' => 'delete',
									'old' => 'Just less <del class="diffchange">text</del>.',
									'oldline' => 1
								],
								[
									'action' => 'add',
									'new' => 'Just less.',
									'newline' => 1
								]
							]
					],
			],
			[
				'before' =>
<<<TEXT
Just multi-line text.
Line number 2.
TEXT
				,
				'after' =>
<<<TEXT
Just multi-line text.
Line number 1.5.
Line number 2.
TEXT
				,
				'result' =>
					[
						1 => [
							[
								'action' => 'copy',
								'copy' => 'Just multi-line text.',
								'oldline' => 1,
								'newline' => 1
							]
						],
						2 =>
							[
								[
									'action' => 'add',
									'new' => '<ins class="diffchange">Line number 1.5.</ins>',
									'newline' => 2
								],
								[
									'action' => 'copy',
									'copy' => 'Line number 2.',
									'oldline' => 2,
									'newline' => 3
								]
							]
					],
			],
			[
				'before' =>
<<<TEXT
Just multi-line text.
Line number 1.5.
Line number 2.
TEXT
				,
				'after' =>
<<<TEXT
Just multi-line text.
Line number 1.5.
TEXT
				,
				'result' =>
					[
						1 => [
							[
								'action' => 'copy',
								'copy' => "Just multi-line text.\nLine number 1.5.",
								'oldline' => 1,
								'newline' => 1
							]
						],
						3 =>
							[
								[
									'action' => 'delete',
									'old' => '<del class="diffchange">Line number 2.</del>',
									'oldline' => 3
								]
							]
					],
			],
			[
				'before' =>
<<<TEXT
Just multi-line text.
Line number 1.5.
Line number 2.
TEXT
				,
				'after' =>
<<<TEXT
Just multi-line test.
Line number 2.
Line number 3.
TEXT
				,
				'result' =>
					[
						1 =>
							[
								[
									'action' => 'delete',
									'old' =>
<<<TEXT
Just multi-line <del class="diffchange">text.</del>
<del class="diffchange">Line number 1.5</del>.
TEXT
									,
									'oldline' => 1
								],
								[
									'action' => 'add',
									'new' => 'Just multi-line <ins class="diffchange">test</ins>.',
									'newline' => 1
								]
							],
						3 => [
							[
								'action' => 'copy',
								'copy' => 'Line number 2.',
								'oldline' => 3,
								'newline' => 2
							]
						],
						4 =>
							[
								[
									'action' => 'add',
									'new' => '<ins class="diffchange">Line number 3.</ins>',
									'newline' => 3

								]
							],
					],
			],
			[
				'before' =>
<<<TEXT
Just multi-line text.
To change number 2.
To change number 3.
TEXT
				,
				'after' =>
<<<TEXT
Just multi-line test.
Line number 2 changed.
Line number 3 also changed.
TEXT
				,
				'result' =>
					[
						1 =>
							[
								[
									'action' => 'delete',
									'old' =>
<<<TEXT
<del class="diffchange">Just multi-line text.
To change number 2.
To change number 3.</del>
TEXT
									,
									'oldline' => 1
								],
								[
									'action' => 'add',
									'new' =>
<<<TEXT
<ins class="diffchange">Just multi-line test.
Line number 2 changed.
Line number 3 also changed.</ins>
TEXT
									,
									'newline' => 1
								]
							],
					],
			],
			[
				'before' =>
<<<TEXT
Just a multi-line text.
Line number two. This line is quite long!
And that's line number three - even longer than the line before.

Just another line with an empty line above.
TEXT
				,
				'after' =>
<<<TEXT
Just a multi-line text.
Add something new.
Line number two. Now line number three and quite long!
Add more new stuff.
TEXT
				,
				'result' =>
					[
						1 => [
							[
								'action' => 'copy',
								'copy' => 'Just a multi-line text.',
								'oldline' => 1,
								'newline' => 1
							]
						],
						2 =>
							[
								[
									'action' => 'delete',
									'old' =>
<<<TEXT
<del class="diffchange">Line number two. This line is quite long!
And that's line number three - even longer than the line before.
&#160;
Just another line with an empty line above.</del>
TEXT
									,
									'oldline' => 2
								],
								[
									'action' => 'add',
									'new' =>
<<<TEXT
<ins class="diffchange">Add something new.
Line number two. Now line number three and quite long!
Add more new stuff.</ins>
TEXT
									,
									'newline' => 2
								]
							],
					],
			],
			[
				'before' =>
<<<TEXT
Just a multi-line text.
Line number two. This line is quite long!
Line number three.
TEXT
				,
				'after' =>
<<<TEXT
Just a multi-line text.
Line number two. This line is now a bit longer!

And it gets even longer.

Line number three.
TEXT
				,
				'result' =>
					[
						1 => [
							[
								'action' => 'copy',
								'copy' => 'Just a multi-line text.',
								'oldline' => 1,
								'newline' => 1
							]
						],
						2 =>
							[
								[
									'action' => 'delete',
									'old' => 'Line number two. This line is ' .
										'<del class="diffchange">quite long</del>!',
									'oldline' => 2
								],
								[
									'action' => 'add',
									'new' =>
<<<TEXT
Line number two. This line is <ins class="diffchange">now a bit longer</ins>!
&#160;
<ins class="diffchange">And it gets even longer.</ins>
&#160;
TEXT
									,
									'newline' => 2
								],
							],
						3 => [
							[
								'action' => 'copy',
								'copy' => 'Line number three.',
								'oldline' => 3,
								'newline' => 6
							]
						],
					],
			],
		];
	}

	/**
	 * @param string $before
	 * @param string $after
	 * @param array $expectedOutput
	 * @dataProvider provider_testMarkupFormat
	 * @covers LineBasedUnifiedDiffFormatter::format
	 */
	public function testMarkupFormat( $before, $after, $expectedOutput ) {
		$diff = new Diff( explode( "\n", $before ), explode( "\n", $after ) );
		$instance = new LineBasedUnifiedDiffFormatter();
		$output = $instance->format( $diff );
		$this->assertEquals( $expectedOutput, $output );
	}

	public function provider_testMarkupFormat() {
		return [
			[
				'before' => 'Text with [markup] <references />.',
				'after' => 'Text with [markup] <references />.',
				'result' => [
					1 => [
						[
							'action' => 'copy',
							'copy' => 'Text with [markup] &lt;references /&gt;.',
							'oldline' => 1,
							'newline' => 1
						]
					]
				],
			]
		];
	}

}
