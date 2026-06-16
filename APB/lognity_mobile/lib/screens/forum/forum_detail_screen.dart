import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:ui';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import '../user_profile_screen.dart';
import 'package:file_picker/file_picker.dart';
import 'create_forum_screen.dart';

class ForumDetailScreen extends StatefulWidget {
  final int forumId;
  ForumDetailScreen({required this.forumId});

  @override
  _ForumDetailScreenState createState() => _ForumDetailScreenState();
}

class _ForumDetailScreenState extends State<ForumDetailScreen> {
  Map<String, dynamic>? forum;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchDetail();
  }

  void _fetchDetail() async {
    try {
      final response = await ApiService.getForumDetail(widget.forumId);
      setState(() { forum = response; isLoading = false; });
    } catch (e) {
      setState(() { isLoading = false; });
      print(e);
    }
  }

  final _answerController = TextEditingController();
  bool isSubmittingAnswer = false;
  PlatformFile? _selectedAnswerFile;

  void _pickAnswerFile() async {
    FilePickerResult? result = await FilePicker.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png', 'zip', 'doc', 'docx'],
      withData: true,
    );
    if (result != null) setState(() => _selectedAnswerFile = result.files.first);
  }

  void _submitAnswer() async {
    final text = _answerController.text.trim();
    if (text.isEmpty && _selectedAnswerFile == null) return;
    setState(() => isSubmittingAnswer = true);
    try {
      await ApiService.addAnswer(widget.forumId, text, file: _selectedAnswerFile);
      _answerController.clear();
      _selectedAnswerFile = null;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Jawaban terkirim!')));
      _fetchDetail();
    } catch (e) {
      AppTheme.showCustomErrorDialog(context, 'Gagal Mengirim Jawaban', e.toString());
    } finally {
      setState(() => isSubmittingAnswer = false);
    }
  }

  void _editForum() async {
    final result = await Navigator.push(context, MaterialPageRoute(builder: (_) => CreateForumScreen(initialForum: forum)));
    if (result == true) { setState(() { isLoading = true; }); _fetchDetail(); }
  }

  void _deleteForum() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Hapus Request?', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: Text('Semua jawaban juga akan terhapus.', style: GoogleFonts.poppins()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(context, true), child: Text('Hapus', style: TextStyle(color: Colors.red))),
        ],
      ),
    );
    if (confirm == true) {
      try {
        await ApiService.deleteForum(widget.forumId);
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Forum berhasil dihapus.')));
      } catch (e) {
        AppTheme.showCustomErrorDialog(context, 'Gagal Menghapus', e.toString());
      }
    }
  }

  void _deleteAnswer(int answerId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Hapus Komentar?', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: Text('Komentar beserta lampirannya akan dihapus.', style: GoogleFonts.poppins()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: Text('Batal')),
          TextButton(onPressed: () => Navigator.pop(context, true), child: Text('Hapus', style: TextStyle(color: Colors.red))),
        ],
      ),
    );
    if (confirm == true) {
      setState(() { isLoading = true; });
      try { 
        await ApiService.deleteAnswer(answerId); 
        _fetchDetail(); 
      } catch (e) {
        setState(() { isLoading = false; });
        AppTheme.showCustomErrorDialog(context, 'Gagal Menghapus', e.toString());
      }
    }
  }

  void _editAnswer(Map<String, dynamic> answer) {
    final editController = TextEditingController(text: answer['content']);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Edit Komentar', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: TextField(controller: editController, maxLines: 4, decoration: InputDecoration(border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)))),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: Text('Batal')),
          ElevatedButton(
            onPressed: () async {
              Navigator.pop(context);
              setState(() { isLoading = true; });
              try { await ApiService.updateAnswer(answer['interaction_id'], editController.text); _fetchDetail(); }
              catch (e) { setState(() { isLoading = false; }); AppTheme.showCustomErrorDialog(context, 'Gagal Memperbarui', e.toString()); }
            },
            child: Text('Simpan'),
          ),
        ],
      ),
    );
  }

  Future<void> _toggleUpvote() async {
    try {
      final res = await ApiService.upvoteForum(widget.forumId);
      setState(() {
        forum!['upvotes_count'] = res['upvotes_count'];
        forum!['is_upvoted'] = !(forum!['is_upvoted'] ?? false);
      });
    } catch (e) {
      AppTheme.showCustomErrorDialog(context, 'Gagal Upvote', e.toString().replaceAll('Exception: ', ''));
    }
  }

  Future<void> _acceptAnswer(int interactionId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Terima Jawaban?', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: Text('Tandai sebagai jawaban terbaik? (+50 Poin)', style: GoogleFonts.poppins()),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context, false), child: Text('Batal')),
          ElevatedButton(onPressed: () => Navigator.pop(context, true), child: Text('Terima')),
        ],
      ),
    );
    if (confirm == true) {
      setState(() => isLoading = true);
      try {
        final res = await ApiService.acceptAnswer(interactionId);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
        _fetchDetail();
      } catch (e) {
        setState(() => isLoading = false);
        AppTheme.showCustomErrorDialog(context, 'Gagal', e.toString());
      }
    }
  }

  void _showReportDialog(String type, int targetId) {
    final reportController = TextEditingController();
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: Text('Laporkan Konten', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: TextField(controller: reportController, maxLines: 3, decoration: InputDecoration(hintText: 'Contoh: Spam, Kata kasar...', border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)))),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              if (reportController.text.trim().isEmpty) return;
              try {
                final res = await ApiService.reportContent(type, targetId, reportController.text.trim());
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
              } catch (e) {
                Navigator.pop(context);
                AppTheme.showCustomErrorDialog(context, 'Gagal Melaporkan', e.toString());
              }
            },
            child: Text('Laporkan'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    if (isLoading) return Scaffold(body: Center(child: CircularProgressIndicator()));
    if (forum == null) return Scaffold(body: Center(child: Text('Forum tidak ditemukan')));

    final answers = forum!['answers'] as List<dynamic>? ?? [];
    final bool isOwner = forum!['is_owner'] ?? false;
    final bool canDelete = forum!['can_delete'] ?? false;

    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(56),
        child: Container(
          decoration: BoxDecoration(
            gradient: isDark ? null : AppTheme.heroGradient,
            color: isDark ? AppTheme.bgDark : null,
          ),
          child: AppBar(
            backgroundColor: Colors.transparent,
            elevation: 0,
            title: Text('Detail Forum', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18)),
            actions: [
              if (isOwner) IconButton(icon: const Icon(Icons.edit_rounded), onPressed: _editForum),
              if (canDelete) IconButton(icon: const Icon(Icons.delete_rounded, color: Colors.redAccent), onPressed: _deleteForum),
              IconButton(icon: const Icon(Icons.flag_rounded, color: Colors.orangeAccent), onPressed: () => _showReportDialog('request', widget.forumId)),
            ],
          ),
        ),
      ),
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
          Column(
            children: [
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ── Question Card ──
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: AppTheme.glassDecoration(isDark, borderRadius: 20),
                        child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 20,
                              backgroundColor: isDark ? Colors.grey.shade700 : Colors.grey.shade200,
                              backgroundImage: forum!['user']?['profil_url'] != null ? NetworkImage(ApiService.parseUrl(forum!['user']!['profil_url'])) : null,
                              child: forum!['user']?['profil_url'] == null ? Icon(Icons.person, color: Colors.grey.shade500) : null,
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  GestureDetector(
                                    onTap: () {
                                      if (forum!['user_id'] != null) Navigator.push(context, MaterialPageRoute(builder: (_) => UserProfileScreen(userId: forum!['user_id'])));
                                    },
                                    child: Text(forum!['user']?['username'] ?? 'User', style: GoogleFonts.poppins(color: AppTheme.lognity600, fontWeight: FontWeight.w600, fontSize: 14)),
                                  ),
                                  Container(
                                    margin: const EdgeInsets.only(top: 4),
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                    decoration: BoxDecoration(color: AppTheme.lognity500.withOpacity(0.1), borderRadius: BorderRadius.circular(6)),
                                    child: Text(forum!['category'] ?? 'Umum', style: GoogleFonts.poppins(fontSize: 10, color: AppTheme.lognity600, fontWeight: FontWeight.w600)),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        Text(forum!['description'] ?? '', style: GoogleFonts.poppins(fontSize: 15, height: 1.6, color: isDark ? Colors.white : Colors.grey.shade800)),
                        if (forum!['attachment_url'] != null) ...[
                          const SizedBox(height: 16),
                          GestureDetector(
                            onTap: () async {
                              final url = Uri.parse(ApiService.parseUrl(forum!['attachment_url']));
                              if (await canLaunchUrl(url)) await launchUrl(url, mode: LaunchMode.externalApplication);
                            },
                            child: Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(color: AppTheme.lognity500.withOpacity(0.08), borderRadius: BorderRadius.circular(12), border: Border.all(color: AppTheme.lognity500.withOpacity(0.2))),
                              child: Row(
                                children: [
                                  const Icon(Icons.attach_file_rounded, size: 18, color: AppTheme.lognity600),
                                  const SizedBox(width: 8),
                                  Expanded(child: Text('Lihat Lampiran', style: GoogleFonts.poppins(fontSize: 13, color: AppTheme.lognity600, fontWeight: FontWeight.w600))),
                                  const Icon(Icons.open_in_new_rounded, size: 16, color: AppTheme.lognity500),
                                ],
                              ),
                            ),
                          ),
                        ],
                        const SizedBox(height: 16),
                        // Upvote button
                        GestureDetector(
                          onTap: _toggleUpvote,
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                            decoration: BoxDecoration(
                              color: (forum!['is_upvoted'] == true) ? Colors.orange.shade50 : (isDark ? Colors.grey.shade800 : Colors.grey.shade50),
                              borderRadius: BorderRadius.circular(24),
                              border: Border.all(color: (forum!['is_upvoted'] == true) ? Colors.orange.shade300 : (isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.thumb_up_rounded, size: 18, color: (forum!['is_upvoted'] == true) ? Colors.orangeAccent : Colors.grey.shade500),
                                const SizedBox(width: 6),
                                Text('Upvote (${forum!['upvotes_count'] ?? 0})', style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w600, color: (forum!['is_upvoted'] == true) ? Colors.orange : Colors.grey.shade600)),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),

                  // ── Answers Header ──
                  Row(
                    children: [
                      Container(width: 4, height: 20, decoration: BoxDecoration(color: AppTheme.lognity500, borderRadius: BorderRadius.circular(2))),
                      const SizedBox(width: 8),
                      Text('Komentar (${answers.length})', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 17, color: isDark ? Colors.white : Colors.grey.shade800)),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // ── Answer Cards ──
                  ...answers.map((ans) => _buildAnswerCard(ans, isOwner, isDark)).toList(),
                ],
              ),
            ),
          ),

          // ── Comment Input ──
          Container(
            padding: const EdgeInsets.fromLTRB(12, 8, 12, 12),
            decoration: AppTheme.glassDecoration(isDark, borderRadius: 0).copyWith(
              border: Border(top: BorderSide(color: isDark ? Colors.white.withOpacity(0.05) : Colors.white.withOpacity(0.5))),
            ),
            child: SafeArea(
              child: Row(
                children: [
                  GestureDetector(
                    onTap: _pickAnswerFile,
                    child: Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: _selectedAnswerFile != null ? AppTheme.lognity500.withOpacity(0.1) : (isDark ? Colors.grey.shade800 : Colors.grey.shade100),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Icon(Icons.attach_file_rounded, size: 20, color: _selectedAnswerFile != null ? AppTheme.lognity500 : Colors.grey.shade500),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: TextField(
                      controller: _answerController,
                      decoration: InputDecoration(
                        hintText: _selectedAnswerFile != null ? 'File terpilih. Tulis jawaban...' : 'Tulis komentar...',
                        hintStyle: GoogleFonts.poppins(fontSize: 13),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(20), borderSide: BorderSide.none),
                        filled: true,
                        fillColor: isDark ? Colors.grey.shade800 : Colors.grey.shade100,
                        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                        isDense: true,
                      ),
                      maxLines: null,
                    ),
                  ),
                  const SizedBox(width: 8),
                  isSubmittingAnswer
                      ? const SizedBox(width: 36, height: 36, child: CircularProgressIndicator(strokeWidth: 2))
                      : GestureDetector(
                          onTap: _submitAnswer,
                          child: Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(gradient: AppTheme.primaryGradient, borderRadius: BorderRadius.circular(14)),
                            child: const Icon(Icons.send_rounded, size: 20, color: Colors.white),
                          ),
                        ),
                ],
              ),
            ),
          ),
        ],
      ),
        ],
      ),
    );
  }

  Widget _buildAnswerCard(Map<String, dynamic> ans, bool isForumOwner, bool isDark) {
    final bool isAccepted = ans['is_accepted_answer'] == 1 || ans['is_accepted_answer'] == true;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: AppTheme.glassDecoration(isDark, borderRadius: 16).copyWith(
        border: Border.all(color: isAccepted ? Colors.green.shade300 : (isDark ? Colors.white.withOpacity(0.05) : Colors.white.withOpacity(0.5)), width: isAccepted ? 2.0 : 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 14,
                backgroundColor: isDark ? Colors.grey.shade700 : Colors.grey.shade200,
                backgroundImage: ans['user']?['profil_url'] != null ? NetworkImage(ApiService.parseUrl(ans['user']!['profil_url'])) : null,
                child: ans['user']?['profil_url'] == null ? Icon(Icons.person, size: 14, color: Colors.grey.shade500) : null,
              ),
              const SizedBox(width: 8),
              GestureDetector(
                onTap: () {
                  if (ans['user_id'] != null) Navigator.push(context, MaterialPageRoute(builder: (_) => UserProfileScreen(userId: ans['user_id'])));
                },
                child: Text(ans['user']?['username'] ?? 'User', style: GoogleFonts.poppins(color: AppTheme.lognity600, fontWeight: FontWeight.w600, fontSize: 13)),
              ),
              if (isAccepted) ...[
                const SizedBox(width: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                  decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(6), border: Border.all(color: Colors.green.shade300)),
                  child: Row(mainAxisSize: MainAxisSize.min, children: [
                    Icon(Icons.check_circle_rounded, size: 12, color: Colors.green.shade600),
                    const SizedBox(width: 3),
                    Text('Terbaik', style: GoogleFonts.poppins(fontSize: 9, fontWeight: FontWeight.w600, color: Colors.green.shade700)),
                  ]),
                ),
              ],
              const Spacer(),
              if (ans['is_owner'] == true)
                _miniActionBtn(Icons.edit_rounded, Colors.blue, () => _editAnswer(ans)),
              if (ans['can_delete'] == true)
                _miniActionBtn(Icons.delete_rounded, Colors.red, () => _deleteAnswer(ans['interaction_id'])),
              _miniActionBtn(Icons.flag_rounded, Colors.orange, () => _showReportDialog('interaction', ans['interaction_id'])),
            ],
          ),
          const SizedBox(height: 10),
          Text(ans['content'] ?? '', style: GoogleFonts.poppins(fontSize: 13, height: 1.5, color: isDark ? Colors.white : Colors.grey.shade700)),
          if (ans['material'] != null && ans['material']['file_path'] != null) ...[
            const SizedBox(height: 10),
            GestureDetector(
              onTap: () async {
                final url = Uri.parse(ApiService.parseUrl(ans['material']['file_path']));
                if (await canLaunchUrl(url)) await launchUrl(url, mode: LaunchMode.externalApplication);
              },
              child: Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(color: AppTheme.lognity500.withOpacity(0.08), borderRadius: BorderRadius.circular(10), border: Border.all(color: AppTheme.lognity500.withOpacity(0.2))),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.attach_file_rounded, size: 14, color: AppTheme.lognity600),
                    const SizedBox(width: 6),
                    Expanded(child: Text('${ans['material']['title'] ?? 'Download'}', style: GoogleFonts.poppins(color: AppTheme.lognity600, fontSize: 12, fontWeight: FontWeight.w600), maxLines: 1, overflow: TextOverflow.ellipsis)),
                  ],
                ),
              ),
            ),
          ],
          if (!isAccepted && isForumOwner && forum!['status'] == 'Open' && ans['user_id'] != forum!['user_id']) ...[
            const SizedBox(height: 10),
            GestureDetector(
              onTap: () => _acceptAnswer(ans['interaction_id']),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.green.shade300)),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  Icon(Icons.check_circle_outline_rounded, size: 16, color: Colors.green.shade600),
                  const SizedBox(width: 6),
                  Text('Terima Jawaban', style: GoogleFonts.poppins(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.green.shade700)),
                ]),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _miniActionBtn(IconData icon, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 4),
        child: Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(color: color.withOpacity(0.08), borderRadius: BorderRadius.circular(6)),
          child: Icon(icon, size: 14, color: color),
        ),
      ),
    );
  }
}
