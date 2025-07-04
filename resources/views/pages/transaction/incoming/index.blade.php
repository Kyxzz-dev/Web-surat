@extends('layout.main')

@section('content')

    <x-breadcrumb
        :values="[__('menu.transaction.menu'), __('menu.transaction.incoming_letter')]">
        <a href="{{ route('transaction.incoming.create') }}" class="btn btn-primary">
            {{ __('menu.general.create') }}
        </a>
    </x-breadcrumb>

    @forelse($data as $letter)
        <x-letter-card :letter="$letter" />
    @empty
        <div class="alert alert-warning text-center">
            {{ __('menu.general.empty') }}
        </div>
    @endforelse

    {!! $data->appends(['search' => $search])->links() !!}

@endsection
