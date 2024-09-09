<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>It's Time to Vote on Your Favorite Thing!</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">It's Time to Vote on Your Favorite Thing!</h1>

        <p>The "{{ $feed->title }}" feed has ended, and now it's time to choose your favorite memory!</p>

        <p>Don't miss this chance to revisit all the great moments and pick the one that stands out the most.</p>

        <a href="{{ route('feed.vote', $feed) }}"
            style="display: inline-block; background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px;">Vote
            Now</a>

        <p style="margin-top: 30px;">Thanks for being part of this journey!<br>{{ config('app.name') }}</p>
    </div>
</body>

</html>
