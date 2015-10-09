<div class="container">
    <div class="grid">
        <div class="grid-item carre-vert">Test pour voir</div>
        <div class="grid-item grid-item--width2 carre-orange">Test pour voir</div>
        <div class="grid-item carre-marron">Test pour voir</div>
        <div class="grid-item carre-vert-fonce">Test pour voir</div>
        <div class="grid-item grid-item--width2 carre-vert-fonce">Test pour voir</div>
        <div class="grid-item carre-marron">Test pour voir</div>
        <div class="grid-item carre-vert">Test pour voir</div>
        <div class="grid-item carre-orange">Test pour voir</div>
    </div>
</div>
<script>
    $('.grid').masonry({
        // options
        itemSelector: '.grid-item'/*,
        columnWidth: '20%'*/
    });
</script>