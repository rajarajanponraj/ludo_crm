@extends('admin::layouts.master')

@section('page_title')
Leave Management
@stop

@section('content-wrapper')
<div class="content full-page">
    <div class="table">
        <div class="table-header">
            <h1>Leave Management</h1>
        </div>

        <div class="table-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaves as $leave)
                        <tr>
                            <td>{{ $leave->user->name }}</td>
                            <td>{{ ucfirst($leave->type) }}</td>
                            <td>
                                {{ $leave->start_date->format('Y-m-d') }} to {{ $leave->end_date->format('Y-m-d') }}
                                ({{ $leave->start_date->diffInDays($leave->end_date) + 1 }} days)
                            </td>
                            <td>{{ $leave->reason }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($leave->status == 'pending')
                                    <form action="{{ route('field_sales.admin.leaves.update', $leave->id) }}" method="post"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form action="{{ route('field_sales.admin.leaves.update', $leave->id) }}" method="post"
                                        style="display: inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $leaves->links() }}
        </div>
    </div>
</div>
@stop