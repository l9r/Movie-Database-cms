<script>

//disqus comment count
var disqus_shortname = '{{{ $disqus }}}';

(function () {
var s = document.createElement('script'); s.async = true;
s.type = 'text/javascript';
s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());

//disqus comment form
(function() {

  var disqus_shortname  = '{{{ $disqus }}}';
  var disqus_identifier = '{{{ isset($title) ? $title->id() : $news->id }}}';

  (function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();

})();
</script>