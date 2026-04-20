import api from './http';

export type AuthUser = {
    id: number;
    nama: string;
    email: string;
    role: string;
    status?: string;
    menu_access?: string[];
    operational_access?: string[];
};

export type AuthLoginPayload = {
    email: string;
    password: string;
    device_name?: string;
};

export type ForgotPasswordPayload = {
    email: string;
};

export type ResetPasswordPayload = {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
};

export type ApiResponse<T> = {
    status: boolean;
    message: string;
    data: T;
};

type LoginResponse = ApiResponse<{
    token: string;
    token_type: string;
    user: AuthUser;
}>;

type MeResponse = ApiResponse<AuthUser>;

type ForgotPasswordResponse = ApiResponse<{
    reset_token?: string;
    email?: string;
}>;

type ResetPasswordResponse = ApiResponse<null>;

export async function login(payload: AuthLoginPayload): Promise<LoginResponse> {
    const response = await api.post<LoginResponse>('/auth/login', payload);
    return response.data;
}

export async function forgotPassword(payload: ForgotPasswordPayload): Promise<ForgotPasswordResponse> {
    const response = await api.post<ForgotPasswordResponse>('/auth/forgot-password', payload);
    return response.data;
}

export async function resetPassword(payload: ResetPasswordPayload): Promise<ResetPasswordResponse> {
    const response = await api.post<ResetPasswordResponse>('/auth/reset-password', payload);
    return response.data;
}

export async function getMe(): Promise<MeResponse> {
    const response = await api.get<MeResponse>('/auth/me');
    return response.data;
}

export async function logout(): Promise<ApiResponse<null>> {
    const response = await api.post<ApiResponse<null>>('/auth/logout');
    return response.data;
}
