# MaximaJax Sample

2015-03-10 by FuzzyJazzy.

### Sample markup 1

	<div name="eq1" class="MaximaJax">
	<pre class="code no-hide"><code>
	exp(2*%pi*%alpha*t);
	tex(%);
	</code></pre>
	</div>

### Result

<!-- MaximaJax -->
<div name="eq1" class="MaximaJax">
<pre class="code no-hide"><code>
exp(2*%pi*%alpha*t);
tex(%);
</code></pre>
</div>
<!-- /MaximaJax -->

### Sample markup 2

	<div name="matrix" class="MaximaJax">
	<pre class="code"><code>
	matrix([1,2,0],[3,1,1],[2,0,2])$
	tex(%)$
	determinant(%o1);
	</code></pre>
	</div>

### Result

<!-- MaximaJax -->
<div name="matrix" class="MaximaJax">
<pre class="code"><code>
matrix([1,2,0],[3,1,1],[2,0,2])$
tex(%)$
determinant(%o1);
</code></pre>
</div>
<!-- /MaximaJax -->



<!--MOU-->
<!-- JavaScript -->
<script type="text/x-mathjax-config">
  MathJax.Hub.Config({ tex2jax: { inlineMath: [['$','$'], ["\\(","\\)"]] } });
</script>
<script type="text/javascript"   src="http://localhost/MathJax/MathJax.js?config=TeX-AMS-MML_SVG"></script>
<!-- MaximaJax --> 
<script type="text/javascript"
	src="http://localhost/js/jquery-2.1.1.min.js">
</script>
<script type="text/javascript"
	src="http://localhost/MaximaJax/MaximaJax.js">
</script>


