{*
	variables that are available:
	- {partners}
*}
<section id="blogRecentCommentsWidget" class="mod">
	<div class="inner">
        {option:partners}
        <ul>
            {iteration:partners}
                <li>
                    <ul>
                        <li>Name: partners.name</li>
                        <li>Img: partners.img</li>
                        <li>Link: partners.link</li>
                    </ul>
                </li>
            {/iteration:partners}
        </ul>
        {/option:partners}
	</div>
</section>
