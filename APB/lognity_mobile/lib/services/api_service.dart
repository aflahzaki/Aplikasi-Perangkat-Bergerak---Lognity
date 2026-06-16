import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'dart:io' show Platform;
import 'dart:typed_data';
import 'package:http_parser/http_parser.dart';
import 'package:file_picker/file_picker.dart';

class ApiService {
  // Ganti dengan IP lokal komputer jika dicoba dari emulator Android (biasanya 10.0.2.2)
  // Atau localhost jika dijalanan sebagai Web / Windows App
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  static String parseUrl(String url) {
    if (url.isEmpty) return url;
    
    // Jika path merupakan relative storage path (seperti 'materials/file.pdf')
    if (!url.startsWith('http')) {
      final host = baseUrl.replaceAll('/api', '');
      url = '$host/storage/$url';
    }

    if (!kIsWeb) {
      if (Platform.isAndroid) {
        return url.replaceAll('localhost', '10.0.2.2').replaceAll('127.0.0.1', '10.0.2.2');
      }
    }
    return url;
  }

  static String parseImageUrl(String url) {
    String fullUrl = url.startsWith('http') ? url : 'http://127.0.0.1:8000/storage/$url';
    return parseUrl(fullUrl);
  }

  static Future<Map<String, String>> _getHeaders() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, String>> getHeadersForMultipart() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    return {
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  // --- AUTH ---
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> register(String username, String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/register'),
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: jsonEncode({'username': username, 'email': email, 'password': password, 'password_confirmation': password}),
    );
    return jsonDecode(response.body);
  }

  static Future<void> logout() async {
    await http.post(
      Uri.parse('$baseUrl/logout'),
      headers: await _getHeaders(),
    );
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
  }

    // --- METADATA ---
  static Future<Map<String, dynamic>> getMetadata() async {
    final prefs = await SharedPreferences.getInstance();
    
    // Check if we have cached metadata
    final cachedStr = prefs.getString('metadata_cache');
    
    try {
      final response = await http.get(Uri.parse('$baseUrl/metadata'), headers: await _getHeaders());
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        await prefs.setString('metadata_cache', jsonEncode(data));
        return data;
      }
    } catch (e) {
      // If network fails, fallback to cache
    }
    
    if (cachedStr != null) {
      return jsonDecode(cachedStr);
    }
    
    // Ultimate fallback if no cache and network fails
    return {
      'categories': {
        'forum': ['Umum'],
        'library': ['Buku']
      },
      'faculties': ['Umum']
    };
  }

  // --- FORUM ---
  static Future<Map<String, dynamic>> getForums({int page = 1, String search = '', String category = '', String faculty = '', String sort = 'latest', String feed = ''}) async {
    String url = '$baseUrl/forums?page=$page&sort=$sort';
    if (search.isNotEmpty) url += '&search=$search';
    if (category.isNotEmpty) url += '&category=$category';
    if (faculty.isNotEmpty) url += '&faculty=$faculty';
    if (feed.isNotEmpty) url += '&feed=$feed';
    
    final response = await http.get(Uri.parse(url), headers: await _getHeaders());
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> getForumDetail(int id) async {
    final response = await http.get(Uri.parse('$baseUrl/forums/$id'), headers: await _getHeaders());
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> upvoteForum(int id) async {
    final response = await http.post(Uri.parse('$baseUrl/forums/$id/upvote'), headers: await _getHeaders());
    final data = jsonDecode(response.body);
    if (response.statusCode >= 400) {
      throw Exception(data['message'] ?? 'Gagal melakukan upvote');
    }
    return data;
  }

  static Future<Map<String, dynamic>> addAnswer(int id, String content, {PlatformFile? file}) async {
    final request = http.MultipartRequest('POST', Uri.parse('$baseUrl/forums/$id/answers'));
    final headers = await getHeadersForMultipart();
    request.headers.addAll(headers);

    request.fields['content'] = content;

    if (file != null) {
      String ext = file.name.split('.').last.toLowerCase();
      MediaType? mediaType;
      if (ext == 'pdf') mediaType = MediaType('application', 'pdf');
      else if (ext == 'png') mediaType = MediaType('image', 'png');
      else if (ext == 'jpg' || ext == 'jpeg') mediaType = MediaType('image', 'jpeg');
      else if (ext == 'zip') mediaType = MediaType('application', 'zip');
      else if (ext == 'doc') mediaType = MediaType('application', 'msword');
      else if (ext == 'docx') mediaType = MediaType('application', 'vnd.openxmlformats-officedocument.wordprocessingml.document');

      if (kIsWeb) {
        request.files.add(http.MultipartFile.fromBytes(
          'file',
          file.bytes!,
          filename: file.name,
          contentType: mediaType,
        ));
      } else {
        request.files.add(await http.MultipartFile.fromPath(
          'file',
          file.path!,
          contentType: mediaType,
        ));
      }
    }

    final response = await request.send();
    final respStr = await response.stream.bytesToString();
    if (response.statusCode == 201 || response.statusCode == 200) {
      return jsonDecode(respStr);
    } else {
      throw Exception('Failed to add answer: $respStr');
    }
  }
  static Future<void> deleteForum(int id) async {
    final response = await http.delete(Uri.parse('$baseUrl/forums/$id'), headers: await _getHeaders());
    if (response.statusCode != 200) {
      throw Exception('Failed to delete forum');
    }
  }

  static Future<void> updateAnswer(int answerId, String content) async {
    final response = await http.post(
      Uri.parse('$baseUrl/answers/$answerId/update'),
      headers: await _getHeaders(),
      body: jsonEncode({'content': content}),
    );
    if (response.statusCode != 200) {
      throw Exception('Failed to update answer');
    }
  }

  static Future<void> deleteAnswer(int answerId) async {
    final response = await http.delete(Uri.parse('$baseUrl/answers/$answerId'), headers: await _getHeaders());
    if (response.statusCode != 200) {
      throw Exception('Failed to delete answer');
    }
  }

  // --- E-LIBRARY ---
  static Future<Map<String, dynamic>> getLibrary({int page = 1, String search = '', String category = ''}) async {
    final url = '$baseUrl/library?page=$page&search=$search&category=$category';
    final response = await http.get(Uri.parse(url), headers: await _getHeaders());
    return jsonDecode(response.body);
  }

  // --- DASHBOARD ---
  static Future<Map<String, dynamic>> getDashboardStats() async {
    final response = await http.get(Uri.parse('$baseUrl/dashboard'), headers: await _getHeaders());
    return jsonDecode(response.body);
  }

  // --- USER PROFILE ---
  static Future<Map<String, dynamic>> getUserProfile(int userId) async {
    final response = await http.get(Uri.parse('$baseUrl/users/$userId'), headers: await _getHeaders());
    if (response.statusCode != 200) throw Exception('Failed to load profile');
    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> changePassword(String oldPassword, String newPassword, String newPasswordConfirmation) async {
    final response = await http.post(
      Uri.parse('$baseUrl/user/change-password'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'old_password': oldPassword,
        'new_password': newPassword,
        'new_password_confirmation': newPasswordConfirmation
      }),
    );
    final data = jsonDecode(response.body);
    if (response.statusCode >= 400) {
      throw Exception(data['message'] ?? 'Gagal mengubah password');
    }
    return data;
  }
  static Future<Map<String, dynamic>> updateProfile({String? username, String? email, String? imagePath, Uint8List? imageBytes, String? imageFilename, bool removePhoto = false}) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';

    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/user/profile'));
    request.headers.addAll({
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    });

    if (username != null) request.fields['username'] = username;
    if (email != null) request.fields['email'] = email;
    if (removePhoto) request.fields['remove_photo'] = '1';

    if (imageBytes != null) {
      request.files.add(http.MultipartFile.fromBytes(
        'photo', 
        imageBytes, 
        filename: imageFilename ?? 'profile.jpg',
        contentType: MediaType('image', 'jpeg')
      ));
    } else if (imagePath != null && imagePath.isNotEmpty) {
      if (kIsWeb) {
        final bytes = await http.readBytes(Uri.parse(imagePath));
        request.files.add(http.MultipartFile.fromBytes(
          'photo', 
          bytes, 
          filename: imageFilename ?? 'profile.jpg',
          contentType: MediaType('image', 'jpeg')
        ));
      } else {
        request.files.add(await http.MultipartFile.fromPath(
          'photo', 
          imagePath,
          contentType: MediaType('image', 'jpeg')
        ));
      }
    }

    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    if (response.statusCode != 200) throw Exception('Failed to update profile: ${response.body}');
    return jsonDecode(response.body);
  }

  // --- NEW FEATURES ---

  static Future<Map<String, dynamic>> acceptAnswer(int interactionId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/answers/$interactionId/accept'),
      headers: await _getHeaders(),
    );
    final data = jsonDecode(response.body);
    if (response.statusCode >= 400) {
      throw Exception(data['message'] ?? 'Gagal menerima jawaban');
    }
    return data;
  }

  static Future<Map<String, dynamic>> reportContent(String type, int targetId, String reason) async {
    final response = await http.post(
      Uri.parse('$baseUrl/report'),
      headers: await _getHeaders(),
      body: jsonEncode({
        'target_type': type,
        'target_id': targetId,
        'reason': reason,
      }),
    );
    final data = jsonDecode(response.body);
    if (response.statusCode >= 400) {
      throw Exception(data['message'] ?? 'Gagal melaporkan konten');
    }
    return data;
  }

  static Future<List<dynamic>> getLeaderboard({int limit = 20}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/leaderboard?limit=$limit'),
      headers: await _getHeaders(),
    );
    if (response.statusCode == 200) {
      return jsonDecode(response.body)['leaderboard'];
    }
    throw Exception('Failed to load leaderboard');
  }

  // --- SOCIAL FEATURES ---
  static Future<bool> getFollowStatus(int userId) async {
    final response = await http.get(Uri.parse('$baseUrl/users/$userId/follow-status'), headers: await _getHeaders());
    if (response.statusCode == 200) {
      return jsonDecode(response.body)['is_following'] ?? false;
    }
    return false;
  }

  static Future<void> followUser(int userId) async {
    final response = await http.post(Uri.parse('$baseUrl/users/$userId/follow'), headers: await _getHeaders());
    if (response.statusCode >= 400) {
      throw Exception('Failed to follow user');
    }
  }

  static Future<void> unfollowUser(int userId) async {
    final response = await http.post(Uri.parse('$baseUrl/users/$userId/unfollow'), headers: await _getHeaders());
    if (response.statusCode >= 400) {
      throw Exception('Failed to unfollow user');
    }
  }

  // --- CHAT FEATURES ---
  static Future<List<dynamic>> getChats() async {
    final response = await http.get(Uri.parse('$baseUrl/chats'), headers: await _getHeaders());
    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }
    throw Exception('Failed to load chats');
  }

  static Future<List<dynamic>> getChatMessages(int userId) async {
    final response = await http.get(Uri.parse('$baseUrl/chats/$userId/messages'), headers: await _getHeaders());
    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    }
    throw Exception('Failed to load messages');
  }

  static Future<Map<String, dynamic>> sendMessage(int userId, String message) async {
    final response = await http.post(
      Uri.parse('$baseUrl/chats/$userId/messages'),
      headers: await _getHeaders(),
      body: jsonEncode({'message': message}),
    );
    if (response.statusCode == 200 || response.statusCode == 201) {
      return jsonDecode(response.body);
    }
    throw Exception('Failed to send message');
  }

  static Future<void> deleteChat(int userId) async {
    await http.delete(
      Uri.parse('$baseUrl/chats/$userId'),
      headers: await _getHeaders(),
    );
  }
}
