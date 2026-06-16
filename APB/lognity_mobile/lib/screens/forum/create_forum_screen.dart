import 'package:flutter/material.dart';
import 'package:file_picker/file_picker.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../services/api_service.dart';
import '../../theme/app_theme.dart';
import 'package:http/http.dart' as http;
import 'dart:io';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:http_parser/http_parser.dart';

class CreateForumScreen extends StatefulWidget {
  final Map<String, dynamic>? initialForum;
  CreateForumScreen({this.initialForum});

  @override
  _CreateForumScreenState createState() => _CreateForumScreenState();
}

class _CreateForumScreenState extends State<CreateForumScreen> {
  final _formKey = GlobalKey<FormState>();
  final _titleController = TextEditingController();
  final _contentController = TextEditingController();
  final _facultyController = TextEditingController();
  
  String? _selectedCategory;
  String? _selectedFaculty;
  PlatformFile? _selectedFile;
  bool _isLoading = false;
  bool _isLoadingMetadata = true;

  List<String> _categories = [];
  List<String> _faculties = [];

  @override
  void initState() {
    super.initState();
    _fetchMetadata();
  }

  void _fetchMetadata() async {
    try {
      final data = await ApiService.getMetadata();
      setState(() {
        _categories = List<String>.from(data['categories']['forum'] ?? []);
        _faculties = List<String>.from(data['faculties'] ?? []);
        _isLoadingMetadata = false;
      });

      if (widget.initialForum != null) {
        _titleController.text = widget.initialForum!['course_name'] ?? '';
        _contentController.text = widget.initialForum!['description'] ?? '';
        
        final initCat = widget.initialForum!['category'];
        if (_categories.contains(initCat)) {
          _selectedCategory = initCat;
        } else if (_categories.isNotEmpty) {
          _selectedCategory = _categories.first;
        }

        final initFac = widget.initialForum!['faculty'];
        if (_faculties.contains(initFac)) {
          _selectedFaculty = initFac;
        }
      }
    } catch (e) {
      setState(() => _isLoadingMetadata = false);
    }
  }

  void _pickFile() async {
    FilePickerResult? result = await FilePicker.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png', 'zip', 'doc', 'docx'],
      withData: true,
    );
    if (result != null) setState(() => _selectedFile = result.files.first);
  }

  void _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedCategory == null) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Pilih kategori!')));
      return;
    }
    setState(() { _isLoading = true; });
    try {
      final isEdit = widget.initialForum != null;
      final url = isEdit 
          ? '${ApiService.baseUrl}/forums/${widget.initialForum!['request_id']}/update' 
          : '${ApiService.baseUrl}/forums';
      final request = http.MultipartRequest('POST', Uri.parse(url));
      final headers = await ApiService.getHeadersForMultipart();
      request.headers.addAll(headers);
      request.fields['course_name'] = _titleController.text;
      request.fields['description'] = _contentController.text;
      request.fields['category'] = _selectedCategory!;
      if (_selectedFaculty != null) {
        request.fields['faculty'] = _selectedFaculty!;
      } else {
        request.fields['faculty'] = _facultyController.text;
      }

      if (_selectedFile != null) {
        String ext = _selectedFile!.name.split('.').last.toLowerCase();
        MediaType? mediaType;
        if (ext == 'pdf') mediaType = MediaType('application', 'pdf');
        else if (ext == 'png') mediaType = MediaType('image', 'png');
        else if (ext == 'jpg' || ext == 'jpeg') mediaType = MediaType('image', 'jpeg');
        else if (ext == 'zip') mediaType = MediaType('application', 'zip');
        else if (ext == 'doc') mediaType = MediaType('application', 'msword');
        else if (ext == 'docx') mediaType = MediaType('application', 'vnd.openxmlformats-officedocument.wordprocessingml.document');

        if (kIsWeb) {
          request.files.add(http.MultipartFile.fromBytes('attachment', _selectedFile!.bytes!, filename: _selectedFile!.name, contentType: mediaType));
        } else {
          request.files.add(await http.MultipartFile.fromPath('attachment', _selectedFile!.path!, contentType: mediaType));
        }
      }

      final response = await request.send();
      if (response.statusCode == 201 || response.statusCode == 200) {
        Navigator.pop(context, true);
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(widget.initialForum != null ? 'Forum berhasil diperbarui!' : 'Forum berhasil dibuat!')));
      } else {
        final respStr = await response.stream.bytesToString();
        AppTheme.showCustomErrorDialog(context, 'Gagal Mengirim', respStr);
      }
    } catch (e) {
      AppTheme.showCustomErrorDialog(context, 'Terjadi Kesalahan', e.toString());
    } finally {
      setState(() { _isLoading = false; });
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(56),
        child: Container(
          decoration: BoxDecoration(gradient: isDark ? null : AppTheme.heroGradient, color: isDark ? AppTheme.bgDark : null),
          child: AppBar(
            backgroundColor: Colors.transparent, elevation: 0,
            title: Text(widget.initialForum != null ? 'Edit Diskusi' : 'Buat Diskusi Baru', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 18)),
          ),
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text('Apa yang ingin kamu diskusikan?', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.w600, color: isDark ? Colors.white : Colors.grey.shade800)),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _contentController,
                      maxLines: 5,
                      style: GoogleFonts.poppins(fontSize: 14),
                      decoration: InputDecoration(
                        hintText: 'Deskripsikan requestmu...',
                        hintStyle: GoogleFonts.poppins(fontSize: 14),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: const BorderSide(color: AppTheme.lognity500, width: 2)),
                      ),
                      validator: (v) => (v == null || v.length < 10) ? 'Isi minimal 10 karakter' : null,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      decoration: InputDecoration(
                        labelText: 'Kategori',
                        labelStyle: GoogleFonts.poppins(),
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                      ),
                      value: _selectedCategory,
                      items: _categories.map((c) => DropdownMenuItem(value: c, child: Text(c, style: GoogleFonts.poppins(fontSize: 14)))).toList(),
                      onChanged: (v) => setState(() => _selectedCategory = v),
                      validator: (v) => v == null ? 'Pilih kategori' : null,
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: _faculties.isNotEmpty
                              ? DropdownButtonFormField<String>(
                                  decoration: InputDecoration(
                                    labelText: 'Fakultas',
                                    labelStyle: GoogleFonts.poppins(fontSize: 14),
                                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                                    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                                  ),
                                  isExpanded: true,
                                  value: _selectedFaculty,
                                  items: _faculties.map((f) => DropdownMenuItem(value: f, child: Text(f, style: GoogleFonts.poppins(fontSize: 12), overflow: TextOverflow.ellipsis))).toList(),
                                  onChanged: (v) => setState(() => _selectedFaculty = v),
                                )
                              : TextFormField(
                                  controller: _facultyController,
                                  style: GoogleFonts.poppins(fontSize: 14),
                                  decoration: InputDecoration(
                                    labelText: 'Fakultas', hintText: 'Misal: FTI', labelStyle: GoogleFonts.poppins(), hintStyle: GoogleFonts.poppins(fontSize: 13),
                                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                                    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                                  ),
                                ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: TextFormField(
                            controller: _titleController,
                            style: GoogleFonts.poppins(fontSize: 14),
                            decoration: InputDecoration(
                              labelText: 'Mata Kuliah', hintText: 'Misal: Aljabar', labelStyle: GoogleFonts.poppins(), hintStyle: GoogleFonts.poppins(fontSize: 13),
                              border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                              enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300)),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    // File picker area
                    GestureDetector(
                      onTap: _pickFile,
                      child: Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: isDark ? Colors.grey.shade800.withOpacity(0.5) : Colors.grey.shade50,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: isDark ? Colors.grey.shade700 : Colors.grey.shade300, style: BorderStyle.solid),
                        ),
                        child: Column(
                          children: [
                            Icon(Icons.cloud_upload_rounded, size: 36, color: _selectedFile != null ? AppTheme.lognity500 : Colors.grey.shade400),
                            const SizedBox(height: 8),
                            Text(
                              _selectedFile != null ? _selectedFile!.name : 'Klik untuk lampirkan file (Opsional)',
                              style: GoogleFonts.poppins(fontSize: 13, color: _selectedFile != null ? AppTheme.lognity600 : Colors.grey.shade500, fontWeight: _selectedFile != null ? FontWeight.w600 : FontWeight.normal),
                              textAlign: TextAlign.center,
                            ),
                            if (_selectedFile == null)
                              Text('PDF, JPG, PNG, ZIP, DOC', style: GoogleFonts.poppins(fontSize: 11, color: Colors.grey.shade400)),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 28),
                    Container(
                      height: 52,
                      decoration: BoxDecoration(
                        gradient: AppTheme.primaryGradient,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [BoxShadow(color: AppTheme.lognity600.withOpacity(0.3), blurRadius: 12, offset: const Offset(0, 4))],
                      ),
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16))),
                        onPressed: _submit,
                        child: Text('Kirim Request', style: GoogleFonts.poppins(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
