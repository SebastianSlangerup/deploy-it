import { usePage } from '@inertiajs/vue3';
import RolesEnum = App.Enums.RolesEnum
import { SharedData } from '@/types';

export function hasRole(role: RolesEnum): boolean {
    const page = usePage<SharedData>();

    return page.props.auth.user.role === role
}
