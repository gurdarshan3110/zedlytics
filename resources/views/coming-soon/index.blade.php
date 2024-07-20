@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .coming-soon-container {
            text-align: center;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .coming-soon-container h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .coming-soon-container p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .social-icons {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .social-icons li {
            margin: 0 10px;
        }
        .social-icons a {
            text-decoration: none;
            color: #333;
            font-size: 1.5em;
        }
    </style>
    <div class="coming-soon-container">
        <h1>Coming Soon</h1>
        <p>We are working hard to bring you a great experience. Stay tuned!</p>
    </div>
 @endsection