<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>@yield('title') - {{config('app.name')}}</title>
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
      <div class="text-6xl text-gray-500 font-light">@yield('code')</div>
      <p class="mb-2 mt-4 text-lg">@yield('message')</p>
      <p class="text-gray-500">
        Sorry, this page is temporarily unavailable, please check the page information
      </p>
      <div class="mt-10">
        <a href="javascript:window.history.back();" class="bg-blue-600 text-white text-sm px-4 py-3 rounded shadow items-center inline-flex hover:shadow-md space -x-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <div>Return to previous page</div>
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
