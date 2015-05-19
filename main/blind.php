<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>script.aculo.us Effects functional test file</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <script src="../js/effect/lib/prototype.js" type="text/javascript"></script>
  <script src="../js/effect/src/scriptaculous.js" type="text/javascript"></script>
  <script src="../js/effect/src/unittest.js" type="text/javascript"></script>
  <style type="text/css" media="screen">
  /* <![CDATA[ */
    #d1 { background-color: #fcb; width: 200px; }
  /* ]]> */
  </style>
  
</head>
<body>
<h1>script.aculo.us Effects functional test file</h1>

<h2>Effect.BlindUp/Effect.BlindDown</h2>

<div id="d1">
  Lorem ipsum dolor sit amet
  <ul>
    <li>test!</li>
    <li>test!</li>
  </ul>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  <img src="icon.png" alt="test!"/>
  Lorem ipsum dolor sit amet
  <ul>
    <li>test!</li>
    <li>test!</li>
  </ul>
  Lorem ipsum dolor sit amet
  <ul>
    <li>test!</li>
    <li>test!</li>
  </ul>
  Lorem ipsum dolor sit amet
  <ul>
    <li>test!</li>
    <li>test!</li>
  </ul>
</div>

<p>
  <a href="#" onclick="Effect.BlindDown('d1');; return false;">BlindDown()</a>
</p>

<p>
  <a href="#" onclick="Effect.BlindUp('d1');; return false;">BlindUp()</a>
</p>


</body>
</html>
