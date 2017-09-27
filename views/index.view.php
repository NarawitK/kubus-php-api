<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>INDEX Test</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
  </head>
  <body>
    <div class="nav">
      <a href="about"><pre>About us</pre></a>
      <a href="contact"><pre>Contact Us</pre></a>
    </div>
    <div>
      <ol>
        <li>Example use of short hand PHP and Double foreach loop thru collection of calss within array.</li>
      </ol>
      <ul>
        <?php foreach($test_query_result as $innerArray):
          foreach ($innerArray as $value) :?>
            <li><?= $value ?></li>
          <? endforeach;
        endforeach; ?>
      </ul>
    </div>

    <div>
      <blockquote>
        <code>
          <ul>
            <?php foreach($test_query_result as $innerArray):
              foreach ($innerArray as $value) :?>
                <li><?= $value ?></li>
              <? endforeach;
            endforeach; ?>
          </ul>
        </code>
      </blockquote>
    </div>

    <script src="js/bootstrap.min.js"></script>
  <!--  <script src="js/jquery-3.2.1.min.js"></script> -->
  </body>
</html>
