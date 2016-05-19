# MaximaJax  <span style="font-size: 0.5em">[English](#english)/[日本語](#japanese)</span> 

Ver.2.0

##<a name="english"></a>What it is?

MaximaJax is a library makes use of [Maxima](http://maxima.sourceforge.net/), a open source computer algebra system, in HTML or Markdown documents.

[MaximaJax Demo](http://wacooky.com/demo/maximajax-demo.html) gives you an idea of how MaximaJax works.

## Getting started
This library requires jQuery and MathJax. To fill this requirement put following HTML lines in &lt;head&gt; elemenet of a HTML document or in the very last part of a Markdown document

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" async
 		src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML">
	</script>
	<script type="text/x-mathjax-config">
		MathJax.Hub.Config({
			tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] },
			TeX: { TagSide: "left" }
		});
	</script>
	<script type="text/javascript" src="http://localhost/XXXXXX/MaximaJax.js"></script>


where "XXXXXX" should be replaced with a folder name in which MaximaJax is installed.  

To get display as show in Fig.1

>![Screen shot 1](images/screenshot-1.png?raw=true)
 Fig.1


put HTML lements

	<script type="math/maxima; name=eq1; no-hide;">
		exp(2*%pi*%alpha*t);
		tex(%);
	</script>
	<script type="math/maxima;">
		...
	</script>

in your HTML or Markdown.

Next example is used in version 1.0 and will be removed.

	<div class="MaximaJax">
			<pre class="code no-hide"><code>
	exp(2*%pi*%alpha*t);
	tex(%);
			</code></pre>
		</div>

		<div name="eq1" class="MaximaJax">
			<pre class="code">
				<code>
				...
				</code>
			</pre>
		</div>
		

Maxima codes and results of the code excusion are displayed in MaximaJax class &lt;div&gt;. 
Initially it is diplayed as a button with label "MAIMXA". And if &lt;pre&gt; element has 'no-hide' class then Maxima codes is displayed as well.

When the button is clicked, another button with label "exec" is shown. In the case of &lt;pre&gt; with 'no-hide' class, codes are displayed at the same time.

When the "exec" button is clicked, results of the code execution are displayed below the button as show in Fig.2. Maxima codes should be placed in &lt;code&gt; element. 


>![Screen shot 2](images/screenshot-2.png?raw=true)
Fig.2

## Have Fun!

---
 
<a name="japanese"></a>
# MaximaJax

## 概要
MaximaJaxはオープンソースの数式処理ソフト[MAXIMA](http://maxima.sourceforge.net/)をMarkdownやHTML文書の中から利用するためのライブラリです。

[MaximaJax Demo](http://wacooky.com/demo/maximajax-demo.html)にアクセスすればどのような動きをするか解るでしょう。

## 使い方
本ライブラリはjQueryとMathJaxが必要です。そのため次のHTMLコードをHTMLまたはMarkdown文書に含めて下さい。

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript" async
 		src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-MML-AM_CHTML">
	</script>
	<script type="text/x-mathjax-config">
		MathJax.Hub.Config({
			tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] },
			TeX: { TagSide: "left" }
		});
	</script>
	<script type="text/javascript" src="http://localhost/XXXXXX/MaximaJax.js"></script>

ここに、 最後の行の"XXXXXX"はMaximaJaxをインストールしたフォルダ名に置き換えて下さい。 

図１に示す表示を得るには、

>![Screen shot 1](images/screenshot-1.png?raw=true)
 図１


HTMLエレメント

	<script type="math/maxima; name=eq1; no-hide;">
		exp(2*%pi*%alpha*t);
		tex(%);
	</script>
	<script type="math/maxima;">
		...
	</script>

を、あなたのHTMLまたはMarkdownに入れてください.

次のサンプルはVer.1.0で使われた書式で、将来は削除される予定です。

		<div class="MaximaJax">
			<pre class="code no-hide"><code>
	exp(2*%pi*%alpha*t);
	tex(%);
			</code></pre>
		</div>

		<div name="eq1" class="MaximaJax">
			<pre class="code">
				<code>
				</code>
			</pre>
		</div>
		

Maximaのコードと、それを実行した結果は'MaximaJax'クラス を指定した&lt;div&gt;の中に表示されます。
初期状態では、それは"MAIMXA"というラベルが付いたボタンとして表示されます。そしてもし&lt;pre&gt;エレメントに'no-hide'クラスが指定されていれば、Maximaのコードも表示されます。

そのボタンがクリックされた時、別の"exec"というラベルが付いたボタンが表示されます。'no-hide'クラスが指定された&lt;pre&gt;であれば同時にMaximaのコードも表示されます。

"exec"ボタンがクリックされた時、そのコードの実行結果が図２のようにボタンの下に表示されます。Maximaのコードは&lt;code&gt;エレメントの中に記述しなくてはなりません。

>![Screen shot 2](images/screenshot-2.png?raw=true)
図２

## 楽しんで下さい!

