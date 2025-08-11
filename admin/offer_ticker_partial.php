<?php if ($offers->num_rows > 0): ?>
<div class="offer-ticker">
    <div class="offer-text">
        <?php
            $offer_texts = [];
            while($offer = $offers->fetch_assoc()) {
                $offer_texts[] = htmlspecialchars($offer['offer_text']);
            }
            echo '<strong>Offers:</strong> ' . implode(' *** ', $offer_texts);
            mysqli_data_seek($offers, 0); // Reset pointer for any other use
        ?>
    </div>
</div>
<?php endif; ?>
