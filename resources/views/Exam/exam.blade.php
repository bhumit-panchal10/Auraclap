@extends('layouts.appnew')

@section('title', 'Exam Result')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Online Exam</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.0/css/bootstrap.min.css">
        <!-- Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.min.js"
            integrity="sha384-ogU6qZl4OdRb4gmLNtf1K/I8MPHbp+Vk6FuF8J7T8VhY/SojTu6+fRKK8fJYcK+6" crossorigin="anonymous">
        </script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css"
            integrity="sha384-TzZzZpL2pOpo4aD+8kpjYaSclT4jz9YoKbF6yXk6/c1Xw15j1h0Xqz5f5T0v5q5" crossorigin="anonymous">


        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f8f9fa;
            }


            .question-container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                text-align: center;
                color: #0393be;
            }

            .question-text {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 15px;
            }

            .option-box {
                background: #f1f1f1;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 10px;
                cursor: pointer;
                display: flex;
                align-items: center;
                transition: background 0.3s;
            }

            .option-box:hover {
                background: #ddd;
            }

            input[type="radio"] {
                margin-right: 10px;
            }

            .pagination-container {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-top: 15px;
            }

            .pagination-container a,
            .pagination-container span {
                padding: 8px 15px;
                margin: 3px;
                border: 1px solid #000;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                color: black;
                background: white;
            }

            .pagination-container a:hover {
                background: black;
                color: white;
            }

            .pagination-container .active {
                background: black;
                color: white;
            }

            .btn {
                background-color: #0393be;
                color: white;

            }

            .btn-nav {
                padding: 12px 20px;
                font-size: 16px;
                border-radius: 5px;
            }

            .bluetxt {
                color: #0393be !important;
            }
        </style>
    </head>

    <body>

        <div class="question-container">
            <h3 class="text-center bluetxt">Exam</h3>
            <p class="question-text">Question {{ $question->Exam_details_id }}: {{ $question->question }}</p>

            <form method="POST" action="{{ $isLastQuestion ? route('exam.final.submit') : route('exam.next') }}">
                @csrf
                <input type="hidden" name="exam_detail_id" value="{{ $question->Exam_details_id }}">
                <input type="hidden" name="Exam_Result_id" value="{{ $ExamResult->Exam_Result_id ?? '' }}">
                <input type="hidden" name="exam_id" value="{{ $question->exam_id }}">
                <input type="hidden" name="Exam_User_id" value="{{ $examUserid }}">

                @if ($question->option_1)
                    <label class="option-box">
                        <input type="radio" name="answer" value="{{ $question->option_1 }}"
                            @if (
                                !empty($ExamResult) &&
                                    $ExamResult->Exam_user_id == $examUserid &&
                                    $question->option_1 == $ExamResult->Answer_in_option) checked @endif>
                        {{ $question->option_1 }}
                    </label>
                @endif


                @if ($question->option_2)
                    <label class="option-box">
                        <input type="radio" name="answer" value="{{ $question->option_2 }}"
                            @if (
                                !empty($ExamResult) &&
                                    $ExamResult->Exam_user_id == $examUserid &&
                                    $question->option_2 == $ExamResult->Answer_in_option) checked @endif>
                        {{ $question->option_2 }}
                    </label>
                @endif

                @if ($question->option_3)
                    <label class="option-box">
                        <input type="radio" name="answer" value="{{ $question->option_3 }}"
                            @if (
                                !empty($ExamResult) &&
                                    $ExamResult->Exam_user_id == $examUserid &&
                                    $question->option_3 == $ExamResult->Answer_in_option) checked @endif>
                        {{ $question->option_3 }}
                    </label>
                @endif

                @if ($question->option_4)
                    <label class="option-box">
                        <input type="radio" name="answer" value="{{ $question->option_4 }}"
                            @if (
                                !empty($ExamResult) &&
                                    $ExamResult->Exam_user_id == $examUserid &&
                                    $question->option_4 == $ExamResult->Answer_in_option) checked @endif>
                        {{ $question->option_4 }}
                    </label>
                @endif


                <div style="display: flex; padding: 15px; justify-content: space-between;"
                    class="d-flex justify-content-between align-items-center mt-3 position-relative">

                    <!-- Back Button -->
                    @if ($previous)
                        <a href="{{ route('exam.question', [$previous->exam_id, $previous->Exam_details_id ?? '']) }}"
                            class="btn btn-secondary">Back</a>
                    @else
                        <span class="btn btn-secondary disabled">Back</span>
                    @endif

                    <!-- Submit Button or Final Submit -->
                    @if ($isLastQuestion)
                        <button type="submit" class="btn btn-danger position-absolute start-50 translate-middle-x"
                            onclick="return confirm('After submitting, you cannot change your answers. Do you want to continue?')">
                            Final Submit
                        </button>
                    @else
                        <button type="submit" class="btn btn-success position-absolute start-50 translate-middle-x">
                            Submit
                        </button>
                    @endif

                    <!-- Next Button -->
                    @if ($next)
                        <a href="{{ route('exam.question', [$next->exam_id, $next->Exam_details_id ?? '']) }}"
                            class="btn btn-primary">Next</a>
                    @else
                        <span class="btn btn-primary disabled">Next</span>
                    @endif
                </div>
                @if ($isLastQuestion)
                    <p class="text-danger text-center mt-2">After submitting, you cannot change your answers.</p>
                @endif
            </form>
        </div>



    </body>

    </html>
@endsection
@section('script')
@endsection
