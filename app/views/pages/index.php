<!-- The header is now loaded automatically -->

<h1 class="gradient-text"><?php echo htmlspecialchars($data['title']); ?></h1>
<p><?php echo htmlspecialchars($data['message']); ?></p>

<div style="margin-top: 2rem;">
    <button class="btn">Sample Button</button>
</div>

<div style="margin-top: 2rem;">
    <p>This is a sample form styled with the new theme.</p>
    <form action="#" method="post" style="max-width: 400px; margin: auto; text-align: left;">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name">

        <label for="options">Choose an option:</label>
        <select name="options" id="options">
            <option value="1">Option 1</option>
            <option value="2">Option 2</option>
        </select>

        <button type="submit" class="btn" style="width: 100%;">Submit</button>
    </form>
</div>


<!-- The footer is now loaded automatically -->
