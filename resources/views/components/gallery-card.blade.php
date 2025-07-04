<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
            <div>
                <h5 class="card-title mb-0 text-uppercase">{{ $extension }}</h5>
                <small>
                    @if($letter->type == 'incoming')
                        <a href="{{ route('transaction.incoming.show', $letter) }}" class="fw-bold">
                            {{ $letter->reference_number }}
                        </a>
                    @else
                        <a href="{{ route('transaction.outgoing.show', $letter) }}" class="fw-bold">
                            {{ $letter->reference_number }}
                        </a>
                    @endif
                </small>
            </div>
            <div class="mt-2 mt-sm-0">
                @if(strtolower($extension) == 'pdf')
                    <i class="bx bxs-file-pdf fs-1"></i>
                @elseif(strtolower($extension) == 'png')
                    <i class="bx bxs-file-png fs-1"></i>
                @elseif(in_array(strtolower($extension), ['jpeg', 'jpg']))
                    <i class="bx bxs-file-jpg fs-1"></i>
                @else
                    <i class="bx bxs-file fs-1"></i>
                @endif
            </div>
        </div>

        <div class="accordion mt-3" id="accordion-{{ str_replace('.', '-', $filename) }}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ str_replace('.', '-', $filename) }}">
                    <button class="accordion-button collapsed text-truncate" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#accordion-id-{{ str_replace('.', '-', $filename) }}"
                        aria-expanded="false"
                        aria-controls="accordion-id-{{ str_replace('.', '-', $filename) }}">
                        {{ $filename }}
                    </button>
                </h2>
                <div id="accordion-id-{{ str_replace('.', '-', $filename) }}"
                    class="accordion-collapse collapse text-center"
                    data-bs-parent="#accordion-{{ str_replace('.', '-', $filename) }}">
                    <div class="accordion-body">
                        @if(strtolower($extension) == 'pdf')
                            <iframe src="{{ route('file.preview', urlencode($filename)) }}"
                                width="100%" height="500px" style="border: none;"></iframe>
                            <a class="btn btn-primary my-3" download href="{{ $path }}">
                                {{ __('menu.general.download') }}
                            </a>
                        @elseif(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                            <img src="{{ $path }}" class="img-fluid my-2" alt="Picture">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
