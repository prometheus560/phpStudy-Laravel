@extends('layouts.app') 
    
@section('title', 'Sort Subjects') 
    
@section('content') 
    
<div class="max-w-3xl mx-auto px-6 py-8"> 
    
    <div class="mb-6"> 
        <h2 class="text-2xl font-bold text-white">Subjects</h2> 
    
    </div> 
    
    <div class="bg-[#14141f] border border-[#23232f] rounded-xl shadow-sm p-5"> 
    
        <h3 class="text-lg font-bold text-white mb-1">Your Current Subjects</h3> 
        
        @if (count($subjects) > 0) 
    
            @php $number = 1; @endphp 
    
            <div class="flex flex-col gap-2.5"> 
    
                @foreach ($subjects as $subject) 
    
                    <div class="flex items-center gap-4 p-4 bg-[#1b1b28] rounded-xl"> 
    
                        <div class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold shrink-0"> 
                            {{ $number }} 
                        </div> 
    
                        <div class="font-bold text-white"> 
                            {{ $subject->subject_name }} 
                        </div> 
    
                    </div> 
    
                    @php $number++; @endphp 
    
                @endforeach 
    
            </div> 
    
        @else 
    
            <div class="text-center py-16"> 
                <h4 class="font-semibold text-white">No subjects found</h4> 
                <p class="text-gray-400 text-sm mt-1">You don't have any subjects yet.</p> 
    
                <div class="mt-4"> 
                    <a href="{{ route('subjects.index') }}" 
                       class="inline-block bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-800"> 
                        Add Subject 
                    </a> 
                </div> 
            </div> 
    
        @endif 
    
    </div> 
    
</div> 
    
@endsection