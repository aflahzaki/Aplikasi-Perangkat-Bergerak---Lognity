import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import 'dart:typed_data';
import '../services/api_service.dart';
import '../theme/app_theme.dart';
import 'forum/forum_detail_screen.dart';
import 'forum/create_forum_screen.dart';

class ProfileScreen extends StatefulWidget {
  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  Map<String, dynamic>? stats;
  Map<String, dynamic>? userProfile;
  List<dynamic> recentRequests = [];
  List<dynamic> recentInteractions = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchProfile();
  }

  void _fetchProfile() async {
    try {
      final response = await ApiService.getDashboardStats();
      final userId = response['user_stats']['user_id'];
      final fullProfile = await ApiService.getUserProfile(userId);
      setState(() {
        stats = response['user_stats'];
        userProfile = fullProfile['user'];
        recentRequests = fullProfile['recent_requests'] ?? [];
        recentInteractions = fullProfile['recent_interactions'] ?? [];
        isLoading = false;
      });
    } catch (e) {
      setState(() { isLoading = false; });
    }
  }

  void _logout() async {
    await ApiService.logout();
    Navigator.of(context).pushNamedAndRemoveUntil('/login', (route) => false);
  }

  void _showEditProfileDialog() {
    final usernameController = TextEditingController(text: stats?['username']);
    final emailController = TextEditingController(text: stats?['email']);
    String? selectedImagePath;
    Uint8List? selectedImageBytes;
    String? selectedImageName;
    bool removePhoto = false;

    showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              title: Text('Edit Profil', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    TextField(controller: usernameController, decoration: InputDecoration(labelText: 'Username', labelStyle: GoogleFonts.poppins())),
                    const SizedBox(height: 8),
                    TextField(controller: emailController, decoration: InputDecoration(labelText: 'Email', labelStyle: GoogleFonts.poppins())),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        ElevatedButton.icon(
                          onPressed: () async {
                            FilePickerResult? result = await FilePicker.pickFiles(type: FileType.image, withData: true);
                            if (result != null) {
                              setDialogState(() {
                                selectedImagePath = result.files.single.path;
                                selectedImageBytes = result.files.single.bytes;
                                selectedImageName = result.files.single.name;
                                removePhoto = false;
                              });
                            }
                          },
                          icon: const Icon(Icons.image_rounded, size: 18),
                          label: Text('Pilih Foto', style: GoogleFonts.poppins(fontSize: 12)),
                        ),
                        if (stats?['profil_url'] != null || selectedImageName != null) ...[
                          const SizedBox(width: 8),
                          TextButton(
                            onPressed: () => setDialogState(() { selectedImagePath = null; selectedImageBytes = null; selectedImageName = null; removePhoto = true; }),
                            child: Text('Hapus', style: GoogleFonts.poppins(color: Colors.red, fontSize: 12)),
                          ),
                        ]
                      ],
                    ),
                    if (selectedImageName != null)
                      Padding(padding: const EdgeInsets.only(top: 8), child: Text('File: $selectedImageName', style: GoogleFonts.poppins(fontSize: 11, color: Colors.grey))),
                  ],
                ),
              ),
              actions: [
                TextButton(onPressed: () => Navigator.pop(context), child: Text('Batal', style: GoogleFonts.poppins())),
                ElevatedButton(
                  onPressed: () async {
                    try {
                      await ApiService.updateProfile(username: usernameController.text, email: emailController.text, imagePath: selectedImagePath, imageBytes: selectedImageBytes, imageFilename: selectedImageName, removePhoto: removePhoto);
                      Navigator.pop(context);
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Profil berhasil diperbarui')));
                      _fetchProfile();
                    } catch (e) {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal memperbarui profil')));
                    }
                  },
                  child: Text('Simpan', style: GoogleFonts.poppins()),
                ),
              ],
            );
          },
        );
      },
    );
  }

  void _showChangePasswordDialog() {
    final oldPw = TextEditingController();
    final newPw = TextEditingController();
    final confirmPw = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Text('Ganti Password', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(controller: oldPw, obscureText: true, decoration: InputDecoration(labelText: 'Password Lama', labelStyle: GoogleFonts.poppins())),
            const SizedBox(height: 8),
            TextField(controller: newPw, obscureText: true, decoration: InputDecoration(labelText: 'Password Baru', labelStyle: GoogleFonts.poppins())),
            const SizedBox(height: 8),
            TextField(controller: confirmPw, obscureText: true, decoration: InputDecoration(labelText: 'Konfirmasi', labelStyle: GoogleFonts.poppins())),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: Text('Batal', style: GoogleFonts.poppins())),
          ElevatedButton(
            onPressed: () async {
              try {
                await ApiService.changePassword(oldPw.text, newPw.text, confirmPw.text);
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Password berhasil diubah')));
              } catch (e) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Gagal mengubah password')));
              }
            },
            child: Text('Simpan', style: GoogleFonts.poppins()),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    if (isLoading) return Scaffold(body: const Center(child: CircularProgressIndicator()));

    final String username = stats?['username'] ?? 'Guest';
    final String email = stats?['email'] ?? 'guest@lognity.com';
    final String level = stats?['level'] ?? 'Mahasiswa';
    final int points = stats?['points'] ?? 0;
    final String? profilUrl = stats?['profil_url'];
    final List badges = userProfile?['badges'] ?? [];

    return Scaffold(
      body: Stack(
        children: [
          // ── Animated Background Orbs ──
          Positioned(
            top: -50,
            left: -50,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.lognity500.withOpacity(isDark ? 0.15 : 0.1),
              ),
            ),
          ),
          Positioned(
            bottom: 100,
            right: -100,
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppTheme.funPink.withOpacity(isDark ? 0.2 : 0.1),
              ),
            ),
          ),
          Positioned.fill(
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 40, sigmaY: 40),
              child: Container(color: Colors.transparent),
            ),
          ),
          // ── Main Content ──
          SingleChildScrollView(
            child: Column(
              children: [
            // ── Gradient Header ──
            Stack(
              clipBehavior: Clip.none,
              children: [
                Container(
                  height: 180,
                  width: double.infinity,
                  decoration: BoxDecoration(
                    gradient: AppTheme.heroGradient,
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(32),
                      bottomRight: Radius.circular(32),
                    ),
                  ),
                  child: Stack(
                    children: [
                      Positioned(top: -30, right: -30, child: Container(width: 120, height: 120, decoration: BoxDecoration(color: Colors.white.withOpacity(0.08), shape: BoxShape.circle))),
                      Positioned(bottom: -20, left: -20, child: Container(width: 80, height: 80, decoration: BoxDecoration(color: AppTheme.funYellow.withOpacity(0.15), shape: BoxShape.circle))),
                      SafeArea(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          child: Row(
                            children: [
                              IconButton(icon: const Icon(Icons.arrow_back_rounded, color: Colors.white), onPressed: () => Navigator.pop(context)),
                              Text('Profil Saya', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18, color: Colors.white)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                Positioned(
                  bottom: -40, left: 0, right: 0,
                  child: Center(
                    child: Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.all(color: isDark ? AppTheme.cardDark : Colors.white, width: 4),
                        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 12)],
                      ),
                      child: CircleAvatar(
                        radius: 48,
                        backgroundColor: isDark ? AppTheme.cardDark : AppTheme.lognity100,
                        backgroundImage: profilUrl != null ? NetworkImage(ApiService.parseUrl(profilUrl)) : null,
                        child: profilUrl == null ? const Icon(Icons.person_rounded, size: 48, color: AppTheme.lognity500) : null,
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 52),

            // ── User Info ──
            Text(username, style: GoogleFonts.poppins(fontSize: 22, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.grey.shade800)),
            const SizedBox(height: 2),
            Text(email, style: GoogleFonts.poppins(fontSize: 13, color: Colors.grey.shade500)),

            // ── Stats Row ──
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 20),
              child: Row(
                children: [
                  Expanded(child: _statPill('Level', level, AppTheme.lognity500, isDark)),
                  const SizedBox(width: 12),
                  Expanded(child: _statPill('Poin', '$points', Colors.amber.shade700, isDark)),
                ],
              ),
            ),

            // ── Badges ──
            if (badges.isNotEmpty) ...[
              _sectionHeader('Badges (${badges.length})', Icons.emoji_events_rounded, Colors.amber),
              const SizedBox(height: 12),
              SizedBox(
                height: 90,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  itemCount: badges.length,
                  itemBuilder: (context, index) {
                    final badge = badges[index];
                    return Container(
                      width: 80, margin: const EdgeInsets.only(right: 10),
                      decoration: BoxDecoration(
                        color: isDark ? Colors.grey.shade800 : AppTheme.lognity50,
                        borderRadius: BorderRadius.circular(14),
                        border: Border.all(color: isDark ? Colors.grey.shade700 : AppTheme.lognity200),
                      ),
                      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
                        Text(badge['icon'] ?? '🏆', style: const TextStyle(fontSize: 28)),
                        const SizedBox(height: 4),
                        Text(badge['name'] ?? '', textAlign: TextAlign.center, style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.w600, color: AppTheme.lognity700), maxLines: 2),
                      ]),
                    );
                  },
                ),
              ),
              const SizedBox(height: 8),
            ],

            // ── Postingan ──
            _sectionHeader('Postingan (${recentRequests.length})', Icons.article_rounded, AppTheme.lognity500),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: recentRequests.isEmpty
                  ? _emptyState('Belum ada postingan.')
                  : Column(
                      children: recentRequests.map((req) => _buildRequestCard(req, isDark)).toList(),
                    ),
            ),

            // ── Interaksi ──
            const SizedBox(height: 8),
            _sectionHeader('Interaksi (${recentInteractions.length})', Icons.chat_rounded, AppTheme.funPurple),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: recentInteractions.isEmpty
                  ? _emptyState('Belum ada interaksi.')
                  : Column(
                      children: recentInteractions.map((inter) => _buildInteractionCard(inter, isDark)).toList(),
                    ),
            ),

            // ── Settings ──
            const SizedBox(height: 16),
            _sectionHeader('Pengaturan', Icons.settings_rounded, Colors.grey),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Container(
                decoration: AppTheme.glassDecoration(isDark, borderRadius: 16),
                child: Column(
                  children: [
                    _settingsRow(Icons.edit_rounded, 'Edit Profil', AppTheme.lognity500, _showEditProfileDialog),
                    Divider(height: 0, indent: 56, color: isDark ? Colors.grey.shade800 : Colors.grey.shade200),
                    _settingsRow(Icons.lock_rounded, 'Ganti Password', AppTheme.funPurple, _showChangePasswordDialog),
                    Divider(height: 0, indent: 56, color: isDark ? Colors.grey.shade800 : Colors.grey.shade200),
                    _settingsRow(Icons.logout_rounded, 'Keluar (Logout)', Colors.red, _logout, isDestructive: true),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 32),
          ],
        ),
      ),
        ],
      ),
    );
  }

  Widget _statPill(String label, String value, Color color, bool isDark) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 16),
      child: Column(
        children: [
          Text(label, style: GoogleFonts.poppins(fontSize: 11, color: Colors.grey.shade500, fontWeight: FontWeight.w500)),
          const SizedBox(height: 4),
          Text(value, style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold, color: color)),
        ],
      ),
    );
  }

  Widget _sectionHeader(String title, IconData icon, Color color) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, size: 16, color: color),
          ),
          const SizedBox(width: 8),
          Text(title, style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _emptyState(String text) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 16),
      child: Text(text, style: GoogleFonts.poppins(color: Colors.grey.shade400, fontSize: 13)),
    );
  }

  Widget _buildRequestCard(Map<String, dynamic> req, bool isDark) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 14),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        title: Text(req['description'] ?? 'Tanpa Deskripsi', maxLines: 2, overflow: TextOverflow.ellipsis, style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13)),
        subtitle: Container(
          margin: const EdgeInsets.only(top: 4),
          child: Row(children: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
              decoration: BoxDecoration(color: AppTheme.lognity500.withOpacity(0.1), borderRadius: BorderRadius.circular(4)),
              child: Text(req['category'] ?? '', style: GoogleFonts.poppins(fontSize: 10, color: AppTheme.lognity600, fontWeight: FontWeight.w500)),
            ),
          ]),
        ),
        trailing: const Icon(Icons.chevron_right_rounded, size: 20),
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ForumDetailScreen(forumId: req['request_id']))),
      ),
    );
  }

  Widget _buildInteractionCard(Map<String, dynamic> inter, bool isDark) {
    final reqDesc = inter['request'] != null ? inter['request']['description'] : 'Request Dihapus';
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 14),
      child: ListTile(
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        leading: Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(color: AppTheme.funPurple.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
          child: const Icon(Icons.chat_bubble_rounded, size: 18, color: AppTheme.funPurple),
        ),
        title: Text(inter['content'] ?? '', maxLines: 2, overflow: TextOverflow.ellipsis, style: GoogleFonts.poppins(fontSize: 13)),
        subtitle: Text('Di: $reqDesc', maxLines: 1, overflow: TextOverflow.ellipsis, style: GoogleFonts.poppins(color: Colors.grey.shade400, fontSize: 11)),
        trailing: const Icon(Icons.chevron_right_rounded, size: 20),
        onTap: () {
          if (inter['request_id'] != null) Navigator.push(context, MaterialPageRoute(builder: (_) => ForumDetailScreen(forumId: inter['request_id'])));
        },
      ),
    );
  }

  Widget _settingsRow(IconData icon, String title, Color color, VoidCallback onTap, {bool isDestructive = false}) {
    return ListTile(
      leading: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
        child: Icon(icon, size: 18, color: color),
      ),
      title: Text(title, style: GoogleFonts.poppins(fontSize: 14, fontWeight: FontWeight.w500, color: isDestructive ? Colors.red : null)),
      trailing: const Icon(Icons.chevron_right_rounded, size: 20),
      onTap: onTap,
    );
  }
}
