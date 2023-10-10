<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EventResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $user = Auth::user();
        $this->resource->load('ticketCategories');

        $data = [
            'id' => $this->event_id,
            'user_uuid' => $this->user_uuid,
            'event_type' => $this->eventType,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'ticket_categories' => TicketCategoryResource::collection(
                $this->ticketCategories
            ),
        ];

        if ($user) {
            $data = $this->addUserData($data, $user);
        }

        return $data;
    }

    private function addUserData($data, $user)
    {
        $isOrganising = $user->isOrganising($this->resource);
        $isStaff = $user->isStaff($this->resource);
        $canEdit = $user->canEdit($this->resource);
        $canValidate = $user->canValidate($this->resource);

        if ($isOrganising || $isStaff) {
            $this->resource->load('staffs.user');
            $staff = $this->staffs->map(function ($staff) {
                $staffUser = new UserBasicResource($staff->user);
                return [
                    'id' => $staff->staff_id,
                    'can_validate' => $staff->can_validate,
                    'can_edit' => $staff->can_edit,
                    'user' => $staffUser,
                ];
            });
            $data['staff'] = $staff;
        }

        $data['is_organising'] = $isOrganising;
        $data['is_staff'] = $isStaff;
        $data['can_edit'] = $canEdit;
        $data['can_validate'] = $canValidate;

        return $data;
    }
}
?>
