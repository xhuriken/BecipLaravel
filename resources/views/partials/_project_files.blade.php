@forelse($files as $file)
    @php
        $hideFile = auth()->user()->role === 'client'
            && $project->is_mask_valided
            && !$file->is_validated;
    @endphp

    @if (!$hideFile)
        <tr data-id="{{ $file->id }}">
            @if(auth()->user()->isBecip())
                <td data-label="Delete" class="icon-cell">
                    <a href="#" class="delete-file-btn" data-delete-url="{{ route('file.delete', $file) }}">
                        <i class="fa-solid fa-trash delete-icon"></i>
                    </a>
                </td>
            @endif

            <td data-label="Révision">
                @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                    <input
                        type="checkbox"
                        class="update-last-index"
                        {{ $file->is_last_index ? 'checked' : '' }}
                    />
                @else
                    <input
                        type="checkbox"
                        class="update-last-index"
                        disabled
                        {{ $file->is_last_index ? 'checked' : '' }}
                    />
                @endif
            </td>

            <td data-label="Nom">{{ $file->name }}</td>

            <td data-label="Type">
                @if(auth()->user()->role == 'drawer' || auth()->user()->role == 'engineer')
                    <select class="file-type-select form-control">
                        <option value="undefine"    {{ $file->type === 'undefine' ? 'selected' : ''}}>Pas définie</option>
                        <option value="coffrage"    {{ $file->type === 'coffrage' ? 'selected' : ''}}>Coffrage</option>
                        <option value="ferraillage" {{ $file->type === 'ferraillage' ? 'selected' : ''}}>Ferraillage</option>
                        <option value="divers"      {{ $file->type === 'divers' ? 'selected' : ''}}>Divers</option>
                    </select>
                @else
                    {{ $file->type }}
                @endif
            </td>

            <td data-label="Commentaire">
                @if($file->user_id == auth()->user()->id)
                    <textarea
                        placeholder="Ajoutez un commentaire..."
                        name="comment[{{ $file->id }}]"
                        class="comment-textarea form-control"
                    >{{ $file->comment }}</textarea>
                @else
                    @php
                        $comment = $file->comment;
                        $shortComment = strlen($comment) > 10
                            ? substr($comment, 0, 10) . '...'
                            : $comment;
                    @endphp

                    {{ $shortComment }}

                    @if(strlen($comment) > 10)
                        <a href="#" class="view-comment" data-comment="{{ $comment }}">Voir plus</a>
                    @endif
                @endif
            </td>

            <td data-label="Déposé par">
                {{ $file->uploadedBy ? $file->uploadedBy->name : 'Inconnu' }}
            </td>

            <td>
                {{ date('d/m/Y', strtotime($file->created_at)) }}
            </td>

            <td data-label="Validé">
                @if(auth()->user()->role == 'engineer')
                    <input type="checkbox" class="is-validated-checkbox" {{ $file->is_validated ? 'checked' : '' }}>
                @else
                    <input type="checkbox" class="is-validated-checkbox" {{ $file->is_validated ? 'checked' : '' }} disabled>
                @endif
            </td>

            <td data-label="Télécharger">
                @if(auth()->user()->role == 'engineer' || $file->is_validated)
                    <input type="checkbox" name="download_files[]" value="{{ $file->id }}">
                @else
                    <input type="checkbox" name="download_files[]" value="{{ $file->id }}" disabled>
                @endif
            </td>

            @if(!$project->is_mask_distributed)
                <td data-label="Distribuer">
                    <input
                        type="checkbox"
                        name="print_files[]"
                        value="{{ $file->id }}"
                        class="distribution-checkbox"
                        {{ ($file->distribution_count >= 1) ? 'disabled' : '' }}
                    />
                </td>
                <td data-label="Impressions">
                    {{ $file->distribution_count }}
                </td>
            @endif
        </tr>
    @endif

@empty
    <tr class="no-data">
        {{-- Ajuste le nombre de <td> selon ton tableau --}}
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        @if(auth()->user()->isBecip())
            <td></td>
        @endif
    </tr>
@endforelse
