@auth
    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
        <div class="mt-4 d-flex">
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary me-2">Edit Project</a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this project?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Delete Project</button>
            </form>
        </div>
    @endif
@endauth
