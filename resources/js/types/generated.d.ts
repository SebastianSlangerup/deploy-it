declare namespace App.Data {
    export type ConfigurationData = {
        id: string;
        name: string;
        description: string;
        cores: number;
        memory: number;
        disk_space: number;
        proxmox_configuration_id: number;
        created_at: any;
        updated_at: any;
    };
    export type InstanceData = {
        id: string;
        name: string;
        description: string;
        created_by: App.Data.UserData;
        is_ready: boolean;
        type: App.Enums.InstanceTypeEnum;
        status: App.Data.InstanceStatusData;
        started_at: any | null;
        stopped_at: any | null;
        suspended_at: any | null;
        created_at: any;
        updated_at: any;
    };
    export type InstanceStatusData = {
        status: App.Enums.InstanceStatusEnum;
        label: string;
        color: string;
    };
    export type NotificationData = {
        title: string;
        description: string;
        notificationType: App.Enums.NotificationTypeEnum;
    };
    export type PackageData = {
        id: string;
        name: string;
        command: string;
        created_at: any;
    };
    export type UserData = {
        id: string;
        name: string;
        email: string;
        instances: { [key: number]: any } | null;
        role: App.Enums.RolesEnum;
    };
}
declare namespace App.Enums {
    export type InstanceStatusEnum = 'started' | 'stopped' | 'suspended' | 'configuring';
    export type InstanceTypeEnum = 'server' | 'container';
    export type NotificationTypeEnum = 'default' | 'success' | 'info' | 'warning' | 'error';
    export type RolesEnum = 'admin' | 'user';
}
