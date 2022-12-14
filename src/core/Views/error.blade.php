<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>{{$msg ?: 'Unknown error'}} - {{config('app.name')}}</title>
  <meta name="msapplication-TileColor" content="#206bc4"/>
  <meta name="theme-color" content="#206bc4"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="mobile-web-app-capable" content="yes"/>
  <meta name="HandheldFriendly" content="True"/>
  <meta name="MobileOptimized" content="320"/>
  <meta name="robots" content="noindex,nofollow,noarchive"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link crossorigin="anonymous" href="https://lib.baomitu.com/tailwindcss/2.2.15/tailwind.min.css" rel="stylesheet">
</head>
<body class="border-t-2 border-blue-600 flex flex-col bg-gray-100 h-screen">
<div class="flex items-center justify-center flex-auto">
  <div class="max-w-2xl py-6">
    <div class="flex items-center justify-center flex-col">
      <div class="text-6xl text-gray-500 font-light">{{$code}}</div>
      <p class="mb-2 mt-4 text-lg">{{$msg ?: 'Unknown error'}}</p>
      <p class="text-gray-500">
        The operation failed, please check the page information, the page will be automatically jumped after <span
          id="time">4</span> seconds
      </p>
      <div class="mt-10 flex gap-4 justify-center">
        <a href="javascript:{{$url ? 'window.location.href=\''.$url.'\'' : 'window.history.back()'}};"
           class="btn-blue flex items-center space-x-3 text-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
               stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          <div>Jump now</div>
        </a>
        <a href="/"
           class="btn flex items-center space-x-3 text-sm">
          <div>Back to homepage</div>
        </a>
      </div>
    </div>
  </div>
</div>
<script language="javascript">
  let num = 4;
  let url = "{{$url}}";
  window.setTimeout("autoJump()", 1000);

  function autoJump() {
    if (num !== 0) {
      document.querySelector('#time').innerHTML = num;
      num--;
      window.setTimeout("autoJump()", 1000);
    } else {
      num = 4;
      if (url) {
        window.location.href = url;
      } else {
        window.history.back();
      }
    }
  }
</script>
</body>
</html>
