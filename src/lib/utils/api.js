// Use dynamic env to avoid build-time failures when env vars are missing
import { env as publicEnv } from '$env/dynamic/public';
import { authStore } from '$lib/stores/auth.js';
import { get } from 'svelte/store';
import { mockData } from './mockData.js';

const API_URL = (publicEnv.PUBLIC_API_URL || 'https://dijitalmentor.de/server/api').replace(/\/$/, '');
const MOCK_MODE = String(publicEnv.PUBLIC_MOCK_MODE || '').trim() === 'true';
const IS_DEV = typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.DEV;

if (IS_DEV) {
  console.debug('API Config:', { API_URL, MOCK_MODE });
}

class APIClient {
  async request(endpoint, options = {}) {
    // Mock Mode Interceptor
    if (MOCK_MODE) {
      console.log(`[MOCK API] ${options.method || 'GET'} ${endpoint}`);
      await new Promise(r => setTimeout(r, 500)); // Simulate latency

      if (endpoint.includes('/subjects/list.php')) {
        return { success: true, data: mockData.subjects };
      }
      
      if (endpoint.includes('/teachers/list.php')) {
        const url = new URL(`http://dummy${endpoint}`);
        const city = url.searchParams.get('city') || (options.params && options.params.city);
        const subject = url.searchParams.get('subject') || (options.params && options.params.subject);
        let max_rate = url.searchParams.get('max_rate') || (options.params && options.params.max_rate);
        
        // Handle "null" string from URLSearchParams
        if (max_rate === 'null') max_rate = null;

        let filteredTeachers = mockData.teachers;

        // Only show approved teachers
        filteredTeachers = filteredTeachers.filter(t => t.approval_status === 'approved');

        if (city && city !== 'undefined') {
          filteredTeachers = filteredTeachers.filter(t => t.city === city);
        }

        if (subject && subject !== 'undefined') {
          filteredTeachers = filteredTeachers.filter(t => 
            Array.isArray(t.subjects) 
              ? t.subjects.some(s => s.slug === subject || s.name.toLowerCase() === subject.toLowerCase())
              : t.subjects.toLowerCase().includes(subject.toLowerCase())
          );
        }

        if (max_rate) {
          const rate = parseFloat(max_rate);
          if (!isNaN(rate)) {
            filteredTeachers = filteredTeachers.filter(t => t.hourly_rate <= rate);
          }
        }

        return { 
          success: true, 
          data: { 
            teachers: filteredTeachers,
            pagination: { total: filteredTeachers.length, page: 1, pages: 1 }
          } 
        };
      }
      
      if (endpoint.includes('/teachers/detail.php')) {
        // Extract ID from query params if passed as object in GET
        // But here endpoint string might contain query params
        const url = new URL(`http://dummy${endpoint}`);
        const id = url.searchParams.get('id') || (options.body && JSON.parse(options.body).id);
        const teacher = mockData.teachers.find(t => t.id == id) || mockData.teachers[0];
        return { success: true, data: teacher };
      }
      
      if (endpoint.includes('/auth/login.php')) {
        return { 
          success: true, 
          data: { 
            token: mockData.user.token, 
            user: mockData.user 
          } 
        };
      }
      
      if (endpoint.includes('/auth/register.php')) {
        const body = JSON.parse(options.body);
        const newUser = {
          ...mockData.user,
          id: Math.floor(Math.random() * 10000), // Generate new ID
          full_name: body.full_name,
          phone: body.phone,
          role: body.role,
          city: body.city,
          zip_code: body.zip_code,
          approval_status: body.role === 'student' ? 'pending' : 'approved'
        };
        
        // If registering as teacher (student role in this codebase), add to teachers list
        if (body.role === 'student') {
          const newTeacher = {
            id: newUser.id,
            full_name: newUser.full_name,
            avatar_url: '',
            approval_status: 'pending',
            university: '',
            department: '',
            city: newUser.city,
            zip_code: newUser.zip_code,
            lat: 52.52,
            lng: 13.405,
            hourly_rate: 0,
            rating_avg: 0,
            review_count: 0,
            subjects: [],
            bio: '',
            graduation_year: null,
            experience_years: 0,
            reviews: []
          };
          mockData.teachers.unshift(newTeacher);
        }

        return { 
          success: true, 
          data: { 
            token: 'mock-token-' + Date.now(), 
            user: newUser 
          } 
        };
      }

      // Lesson Requests Mocking
      if (endpoint.includes('/requests/list.php')) {
        return { success: true, data: mockData.lessonRequests };
      }

      if (endpoint.includes('/requests/my_requests.php')) {
        const { user } = get(authStore);
        const userId = user?.id || 201;
        return { success: true, data: mockData.lessonRequests.filter(r => r.parent_id === userId) };
      }

      if (endpoint.includes('/requests/detail.php')) {
        const url = new URL(`http://dummy${endpoint}`);
        const id = url.searchParams.get('id') || (options.body && JSON.parse(options.body).id);
        const request = mockData.lessonRequests.find(r => r.id == id);
        return { success: true, data: request };
      }

      if (endpoint.includes('/requests/create.php')) {
        const { user } = get(authStore);
        const body = JSON.parse(options.body);
        const newRequest = {
          id: Math.floor(Math.random() * 1000),
          parent_id: user?.id || 201,
          parent_name: user?.full_name || 'Demo Veli',
          ...body,
          status: 'active',
          created_at: new Date().toISOString().split('T')[0]
        };
        mockData.lessonRequests.unshift(newRequest);
        return { success: true, message: 'Talep oluşturuldu', data: newRequest };
      }

      // Message Endpoints
      if (endpoint.includes('/messages/list.php')) {
        return { success: true, data: mockData.conversations };
      }

      if (endpoint.includes('/messages/detail.php')) {
        const url = new URL(`http://dummy${endpoint}`);
        const id = url.searchParams.get('id');
        const messages = mockData.messages.filter(m => m.conversation_id == id);
        return { success: true, data: messages };
      }

      if (endpoint.includes('/messages/send.php')) {
        const body = JSON.parse(options.body);
        const newMessage = {
          id: Math.floor(Math.random() * 10000),
          conversation_id: body.conversation_id,
          sender_id: mockData.user.id,
          text: body.text,
          created_at: new Date().toISOString()
        };
        mockData.messages.push(newMessage);
        
        // Update conversation last message
        const conv = mockData.conversations.find(c => c.id == body.conversation_id);
        if (conv) {
          conv.last_message = body.text;
          conv.last_message_date = newMessage.created_at;
        }
        
        return { success: true, data: newMessage };
      }

      // Default success for other endpoints
      // Blog Endpoints
      if (endpoint === '/blog/list.php') {
        return { success: true, data: mockData.posts };
      }

      if (endpoint.includes('/blog/detail.php')) {
        const url = new URL(`http://dummy${endpoint}`); // Define url here for blog endpoints
        const slug = url.searchParams.get('slug');
        const post = mockData.posts.find(p => p.slug === slug);
        return post ? { success: true, data: post } : { success: false, message: 'Yazı bulunamadı' };
      }

      if (endpoint === '/blog/comment.php') {
        return { success: true, message: 'Yorum eklendi' };
      }

      if (endpoint === '/blog/like.php') {
        return { success: true, message: 'Beğenildi' };
      }

      return { success: false, message: 'Endpoint not found' };
    }

    const { token } = get(authStore);
    
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...(token && { Authorization: `Bearer ${token}` }),
        ...options.headers
      }
    };
    
    try {
      const response = await fetch(`${API_URL}${endpoint}`, config);
      const data = await response.json();
      
      if (!response.ok) throw new Error(data.error || 'Request failed');
      return data;
    } catch (error) {
      if (IS_DEV) console.error('API Error:', error);
      throw error;
    }
  }
  
  get(endpoint, params = {}) {
    const query = new URLSearchParams(params).toString();
    return this.request(query ? `${endpoint}?${query}` : endpoint, { method: 'GET' });
  }
  
  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data)
    });
  }
}

export const api = new APIClient();
